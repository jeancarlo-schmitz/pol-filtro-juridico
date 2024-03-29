<?php

namespace Presentation\Routes;

use Http\Request;
use Exception;
use Application\Exceptions\MethodNotAllowedException;
use Application\Exceptions\NotFoundException;
use Infrastructure\Utils\Sanitizer;

class Router
{
    private $routes = [];
    private $uri;
    private $method;
    private $response;
    private $sanitize;


    public function __construct($routes)
    {
        $this->routes = $routes;
        $this->response = '';
        $this->sanitize = new Sanitizer();
    }

    public function dispatch()
    {
        $request = new Request($this->sanitize);

        $this->uri = $request->getUri();
        $this->method = $request->getMethod();

        $this->redirectRouteIfExists($request);
        $this->redirectRouteWithParamIfExists($request);

        $this->verifyIfMethodAllowed();
        $this->verifyIfRouteNotFound();

        return $this->response;
    }

    private function verifyIfRouteNotFound(){
        if(empty($this->response)) {
            throw new NotFoundException();
        }
    }

    private function redirectRouteIfExists(Request $request){

        if (isset($this->routes[$this->uri])) {
            foreach ($this->routes[$this->uri] as $routeMethod => $handler) {
                if ($this->matchMethod($this->method, $routeMethod)) {
                    $this->response = $this->handle($handler, $request);
                }
            }
        }

    }

    private function redirectRouteWithParamIfExists(Request $request)
    {
        if (empty($this->response)) {
            $routeParams = $this->getRouteWithParams();

            [$uri, $params, $routeParamsNames] = $this->getUriAndParamsFromRouteWithParams($routeParams);

            $this->handleRouteWithParam($uri, $routeParamsNames, $request);
        }
    }

    private function handleRouteWithParam($uri, $routeParamsNames, Request $request){
        if(isset($this->routes[$uri])) {
            foreach ($this->routes[$uri] as $routeMethod => $handler) {
                if ($this->matchMethod($this->method, $routeMethod)) {
                    $params = $this->matchPath($this->uri, $uri);
                    if (!empty($params)) {
                        $this->response = $this->handleWithParams($handler, $routeParamsNames, $params, $request);
                    }
                }
            }
        }
    }

    private function handleWithParams($handler, $routeParamsNames, $params, Request $request)
    {
        [$className, $methodName] = $handler;

        if (class_exists($className)) {
            $classInstance = new $className($request);

            if (method_exists($classInstance, $methodName)) {
                $this->extractRouteParams($routeParamsNames, $params, $request);
                return $classInstance->$methodName();
            }
        }

        throw new Exception("Problema pra lidar com a rota", 1000);
    }

    private function extractRouteParams($paramsNames, $params, Request $request)
    {
        foreach ($paramsNames as $paramName) {
            $value = $this->sanitize->sanitizeAll($params[$paramName]);
            $request->addParam($paramName, $value);
        }
    }

    private function getRouteWithParams()
    {
        $routeParams = [];

        foreach ($this->routes as $route => $methods) {
            if (strpos($route, '{') !== false && isset($methods[$this->method])) {
                $params = $this->extractRouteParamsNames($route);
                $routeParams[] = [
                    'path' => $route,
                    'params' => $params
                ];
            }
        }
        return $routeParams;
    }

    private function extractRouteParamsNames($route)
    {
        preg_match_all('/\{([a-zA-Z0-9-_]+)\}/', $route, $matches);
        return $matches[1];
    }

    private function isMethodToRouteNotRegistered()
    {
        return !isset($this->routes[$this->uri][$this->method]) && isset($this->routes[$this->uri]);
    }

    private function getUriAndParamsFromRouteWithParams($routeParams){
        $uri = '';
        $params = '';
        $routeParamsNames = '';

        if ($routeParams !== null) {
            foreach ($routeParams as $route) {
                $routePath        = $route['path'];
                $routeParamsNames = $route['params'];

                $params = $this->matchPath($this->uri, $routePath);
                if (!empty($params)) {
                    $uri = $routePath;
                    break;
                }
            }
        }

        return array($uri, $params, $routeParamsNames);
    }

    private function isMethodToRouteWithParamNotRegistered()
    {
        $routeParams = $this->getRouteWithParams();
        [$uri, $params, $routeParamsNames] = $this->getUriAndParamsFromRouteWithParams($routeParams);

        if(empty($uri)){
            return false;
        }

        return !isset($this->routes[$uri][$this->method]) && isset($this->routes[$uri]);
    }

    private function verifyIfMethodAllowed(){
        if($this->isMethodToRouteNotRegistered()){
            $this->handleMethodNotAllowed();
        }

        if($this->isMethodToRouteWithParamNotRegistered()){
            $this->handleMethodNotAllowed();
        }
    }

    private function handleMethodNotAllowed(){
        throw new MethodNotAllowedException();
    }

    private function matchMethod($method, $routeMethod){
        return $method === $routeMethod;
    }

    private function matchPath($path, $routePath)
    {
        $pattern = preg_replace('/\//', '\\/', $routePath);
        $pattern = preg_replace('/\{([a-z_-]+)\}/', '(?P<\1>[a-zA-Z0-9-_]+)', $pattern);
        $pattern = '/^' . $pattern . '$/';

        preg_match($pattern, $path, $matches);

        return $matches;
    }

    private function handle($handler, Request $request)
    {
        [$className, $methodName] = $handler;

        if (class_exists($className)) {
            $classInstance = new $className($request);

            if (method_exists($classInstance, $methodName)) {
                return $classInstance->$methodName();
            }
        }

        throw new Exception("Problema pra lidar com a rota", 1000);
    }
}
