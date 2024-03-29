<?php
/**
 * Created by PhpStorm.
 * User: jean.schmitz
 * Date: 05/06/2023
 * Time: 16:50
 */

namespace Application\Exceptions;

use Exception;
use Application\Exceptions\Constants\ValidationExceptionConstants;


class ValidationFieldException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, ValidationExceptionConstants::EMPTY_FIELD_CODE);
    }
}