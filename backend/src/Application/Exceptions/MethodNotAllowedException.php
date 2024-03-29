<?php

namespace Application\Exceptions;

use Exception;
use Application\Exceptions\Constants\HttpExceptionConstants;

class MethodNotAllowedException extends Exception
{
    public function __construct()
    {
        parent::__construct(HttpExceptionConstants::METHOD_NOT_ALLOWED_MESSAGE, HttpExceptionConstants::METHOD_NOT_ALLOWED_CODE);
    }
}