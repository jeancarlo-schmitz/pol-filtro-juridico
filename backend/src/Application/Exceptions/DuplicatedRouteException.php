<?php

namespace Application\Exceptions;

use Exception;
use Application\Exceptions\Constants\HttpExceptionConstants;

class DuplicatedRouteException extends Exception
{
    public function __construct($route)
    {
        $message = sprintf(HttpExceptionConstants::ROUTE_DUPLICATED_MESSAGE, $route);

        parent::__construct($message, HttpExceptionConstants::INTERNAL_SERVER_ERROR_CODE);
    }
}