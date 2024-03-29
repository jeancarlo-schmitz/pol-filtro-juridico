<?php

namespace Application\Exceptions;

use Exception;
use Application\Exceptions\Constants\HttpExceptionConstants;

class UnauthenticatedUserException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, HttpExceptionConstants::UNAUTHORIZED_CODE);
    }
}