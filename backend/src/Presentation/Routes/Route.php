<?php
namespace Presentation\Routes;

use Application\Controllers\DistribuicaoController;
use Application\Controllers\DocumentacaoController;
use Application\Controllers\MovimentacaoController;
use Application\Controllers\PublicacaoController;
use Exception;
use Infrastructure\ExceptionHandler;
use Application\Exceptions\DuplicatedRouteException;

Class Route
{
    private $routes;

    private function get(string $uri, $handler)
    {
        $this->checksIfRouteIsAlreadyRegistered($uri, 'GET');
        $this->routes[$uri]['GET'] = $handler;
    }

    private function post(string $uri, $handler)
    {
        $this->checksIfRouteIsAlreadyRegistered($uri, 'POST');
        $this->routes[$uri]['POST'] = $handler;
    }

    private function put(string $uri, $handler)
    {
        $this->checksIfRouteIsAlreadyRegistered($uri, 'PUT');
        $this->routes[$uri]['PUT'] = $handler;
    }

    private function delete(string $uri, $handler)
    {
        $this->checksIfRouteIsAlreadyRegistered($uri, 'DELETE');
        $this->routes[$uri]['DELETE'] = $handler;
    }

    private function checksIfRouteIsAlreadyRegistered($route, $method){
        if(isset($this->routes[$route][$method])){
            throw new DuplicatedRouteException($route);
        }

        if($this->isRouteWithParams($route)){
            $this->checksIfRouteWithParamsIsAlreadyRegistered($route, $method);
        }
    }

    private function isRouteWithParams($route){
        return strpos($route, '{') !== false;
    }

    private function checksIfRouteWithParamsIsAlreadyRegistered($routeToVerify, $methodToVerify){
        $routeToVerifyWithoutParam = $this->getRouteWithoutParam($routeToVerify);

        foreach ($this->routes as $route => $dataMethod){
            if($this->isRouteWithParams($route)){
                $routeWithoutParam = $this->getRouteWithoutParam($route);
                if(isset($dataMethod[$methodToVerify]) && $routeWithoutParam === $routeToVerifyWithoutParam){
                    throw new DuplicatedRouteException($routeToVerify);
                }
            }
        }
    }

    private function getRouteWithoutParam($routeToVerify){
        preg_match("/(?<route>\/.*)(?<param>\{.*\})/", $routeToVerify, $results);

        return $results['route'];
    }

    public function getAllRoutes(){
        try {
            $this->addGetRoutes();
            $this->addPostRoutes();
            $this->addDeleteRoutes();
            $this->addPutRoutes();

            return $this->routes;
        }catch (Exception $e){
            $exceptionHandler = new ExceptionHandler();
            $response = $exceptionHandler->handle($e);
            $response->send();
        }
    }

    private function addGetRoutes(){
        $this->get("/", [DocumentacaoController::class, 'mostraDocumentacao']);
    }

    private function addPostRoutes(){

        /*PUBBLICACOES*/
        $this->post("/getPublicacoesNaoImportadas", [PublicacaoController::class, 'getPublicacoesNaoImportadas']);
        $this->post("/publicacoesNaoImportadas", [PublicacaoController::class, 'getPublicacoesNaoImportadas']);
        $this->post("/publicacoesConfirmacao", [PublicacaoController::class, 'confirmarRecebimentoPublicacao']);

        //todo adicionar novo filtro, de importada ou não importada
        $this->post("/publicacoesPorDia", [PublicacaoController::class, 'getListaPublicacaoByData']);

        //O nome do CPJ é muito ruim, então criei outra rota, que faz a mesma coisa, mas com um nome diferente
        $this->post("/publicacoesPorData", [PublicacaoController::class, 'getQuantitativoPublicacoesImportadasENaoImportadasByData']);
        $this->post("/getQuantidadePublicacoes", [PublicacaoController::class, 'getQuantitativoPublicacoesImportadasENaoImportadasByData']);

        /*DISTRIBUICAO*/
        $this->post("/getDistribuicoesNaoImportadas", [DistribuicaoController::class, 'getDistribuicoesNaoImportadas']);
        $this->post("/confirmarLeituraDistribuicoes", [DistribuicaoController::class, 'doConfirmarLeituraDistribuicoes']);
        $this->post("/getDistribuicoesPorData", [DistribuicaoController::class, 'getDistribuicoesPorData']);
        $this->post("/getQuantidadeDistribuicoesData", [DistribuicaoController::class, 'getQuantidadeDistribuicoesData']);

        /*MOVIMENTACAO*/
        $this->post("/getMovimentacoesNaoImportadas", [MovimentacaoController::class, 'getMovimentacoesNaoImportadas']);
        $this->post("/confirmarLeituraMovimentacoes", [MovimentacaoController::class, 'doConfirmarLeituraMovimentacoes']);
        $this->post("/getMovimentacoesPorData", [MovimentacaoController::class, 'getMovimentacoesPorData']);
        $this->post("/getQuantidadeMovimentacoesData", [MovimentacaoController::class, 'getQuantidadeMovimentacoesData']);
        $this->post("/getMovimentacoesPorNumeroProcesso", [MovimentacaoController::class, 'getMovimentacoesPorNumeroProcesso']);
    }

    private function addDeleteRoutes(){
    }

    private function addPutRoutes(){
    }
}