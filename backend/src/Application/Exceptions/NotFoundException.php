<?php

namespace Application\Exceptions;

use Exception;
use Application\Exceptions\Constants\HttpExceptionConstants;

class NotFoundException extends exception
{
    public function __construct()
    {
        parent::__construct(HttpExceptionConstants::NOT_FOUND_MESSAGE, HttpExceptionConstants::NOT_FOUND_CODE);
    }
}