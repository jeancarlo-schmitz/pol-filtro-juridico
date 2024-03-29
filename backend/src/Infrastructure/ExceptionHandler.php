<?php

namespace Infrastructure;

use Application\Exceptions\Constants\HttpExceptionConstants;
use Application\Exceptions\Handlers\HttpExceptionHandler;
use Application\Exceptions\InternalServerErrorException;
use Exception;
use Http\Response;

class ExceptionHandler
{
    private $httpExceptionHandler;
    private $tribunalExceptionHandler;

    public function __construct()
    {
        $this->httpExceptionHandler = new HttpExceptionHandler();

        if (!IS_DEV) {
            error_reporting(0);
            ini_set("display_errors", 0);
        }
    }

    public function customErrorHandler($errno, $errstr, $errfile, $errline)
    {
        $statusCode = 500;
        switch ($errno) {
            case E_USER_ERROR:
                $statusCode = 400; // Bad Request
                break;
            case E_USER_WARNING:
            case E_WARNING:
                $statusCode = 422; // Unprocessable Entity
                break;
            case E_USER_NOTICE:
            case E_NOTICE:
                $statusCode = 200; // OK (tratado como aviso)
                break;
        }

        if(IS_DEV){
            pre($errfile);
            pre($errline);
            pred($errstr);
        }

        $response = new Response('', HttpExceptionConstants::INTERNAL_SERVER_ERROR_MESSAGE, $statusCode);
        $response->send();
        exit;
    }

    public function handleFatalError() {
        $error = error_get_last();
        if ($error !== null && $error['type'] === E_ERROR) {

//            if(IS_DEV){
//                pred($error);
//            }

            $response = new Response('', HttpExceptionConstants::INTERNAL_SERVER_ERROR_MESSAGE, HttpExceptionConstants::INTERNAL_SERVER_ERROR_CODE);
            $response->send();
            exit;
        }
    }

    public function handle(Exception $e)
    {

        if ($this->httpExceptionHandler->canHandle($e)) {
            return $response = $this->httpExceptionHandler->handle($e);
        }

        if($e instanceof Exception){
            return new Response('', $e->getMessage(), $e->getCode());
        }

//        if (IS_DEV) {
//            pred($e);
//        }

        return new Response('', HttpExceptionConstants::INTERNAL_SERVER_ERROR_MESSAGE, HttpExceptionConstants::INTERNAL_SERVER_ERROR_CODE);

    }
}