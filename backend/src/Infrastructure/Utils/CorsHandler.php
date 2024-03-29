<?php

namespace Infrastructure\Utils;

class CorsHandler
{
    private $allowedOrigins = [
        "*"
    ];

    public function handleCors()
    {
        $this->setAllowedOriginHeader();
        $this->setAllowedMethodsHeader();
        $this->setAllowedHeadersHeader();
        $this->setCorsMaxAgeHeader();

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit();
        }
    }

    private function setAllowedOriginHeader()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (in_array($origin, $this->allowedOrigins)) {
            header('Access-Control-Allow-Origin: ' . $origin);
        }
    }

    private function setAllowedMethodsHeader()
    {
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    }

    private function setAllowedHeadersHeader()
    {
        header('Access-Control-Allow-Headers: Content-Type');
    }

    private function setCorsMaxAgeHeader()
    {
        header('Access-Control-Max-Age: 86400'); // Cache das configurações CORS por 24 horas
    }
}