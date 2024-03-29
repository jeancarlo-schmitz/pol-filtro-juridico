<?php

namespace Http;

use Infrastructure\Utils\JsonHelper;
use Infrastructure\Utils\Sanitizer;
use Infrastructure\Utils\Utils;

class Request
{
    private $method;
    private $uri;
    private $parameters;
    private $body;

    public function __construct(Sanitizer $sanitize)
    {
        $this->method          = $_SERVER['REQUEST_METHOD'];
        $this->uri             = $_SERVER['REQUEST_URI'];
        $this->body            = file_get_contents('php://input');
        $this->parameters      = $sanitize->sanitizeAll($this->getAllData());
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        if(Utils::isDev()){
            $this->removeLocalServerPath();
        }
        $this->removeQueryParameters();
        return $this->uri;
    }

    public function getParameter(string $name, $default = null)
    {
        return $this->parameters[$name] ?? $default;
    }

    public function getAllParameters()
    {
        return $this->parameters;
    }

    public function addParam(string $name, $value){
        $this->parameters[$name] = $value;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    private function removeQueryParameters(){
        $parts = parse_url($this->uri);
        if (isset($parts['query'])) {
            $this->uri = str_replace('?' . $parts['query'], '', $this->uri);
        }
    }

    private function removeLocalServerPath(){
        $documentRoot = str_replace("/", "\\", DIR);
        $documentRoot = explode("\\", $documentRoot);

        foreach ($documentRoot as $path){
            $this->uri = str_replace("/" . $path, "", $this->uri);
        }
    }

    private function getAllData() {
        $method = $_SERVER['REQUEST_METHOD'];

        $data = [];

        if ($method === 'GET') {
            $data = $_GET;
        } elseif ($method === 'POST') {
            $data = $this->parseRequestBodyAndPostParams();
        } elseif ($method === 'PUT' || $method === 'DELETE') {
            $data = $this->parseRequestBodyAndPostParams();
        }

        return $data;
    }

    private function parseRequestBodyAndPostParams(){

        $data = [];
        if (!empty($this->body)) {
            if ($this->isContentTypeJson()) {
                $data = JsonHelper::jsonToArray($this->body);
            } elseif ($this->isContentTypeFormUrlencoded()) {
                $data = $_POST;
            } else {
                parse_str($this->body, $body);
                $data = $body;
            }
        }

        return $data;
    }

    private function isContentTypeJson()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return strtolower($contentType) === 'application/json';
    }

    private function isContentTypeFormUrlencoded()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return strtolower($contentType) === 'application/x-www-form-urlencoded';
    }
}