<?php

namespace Application\Exceptions;

use Exception;
use Application\Exceptions\Constants\HttpExceptionConstants;

class InternalServerErrorException extends Exception
{
    public function __construct()
    {
        parent::__construct(HttpExceptionConstants::INTERNAL_SERVER_ERROR_MESSAGE, HttpExceptionConstants::INTERNAL_SERVER_ERROR_CODE);
    }
}