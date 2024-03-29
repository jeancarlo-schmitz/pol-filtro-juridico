<?php
namespace Infrastructure\Utils;

use Application\Exceptions\Constants\HttpExceptionConstants;
use Exception;
use DateTime;

class ValidationUtils
{
    public static function notEmpty($value, $fieldName)
    {
        if (empty($value)) {
            throw new Exception("O campo '" . $fieldName . "' não pode ser vazio", HttpExceptionConstants::BAD_REQUEST_CODE);
        }
    }

    public static function mustBeIntVal($value, $fieldName)
    {
        if(preg_match("/[^0-9]/", $value)) {
            throw new Exception("O campo " . $fieldName . ", de valor: " . $value . " precisa ser um Inteiro", HttpExceptionConstants::BAD_REQUEST_CODE);
        }
    }

    public static function applyLengthValidation($value, $fieldName, $validationParams)
    {
        $params = explode(',', $validationParams);
        $minLength = null;
        $maxLength = null;

        foreach ($params as $param) {
            $paramParts = explode('=', $param);
            $paramName = $paramParts[0];
            $paramValue = $paramParts[1];

            if ($paramName === 'min') {
                $minLength = (int) $paramValue;
            } elseif ($paramName === 'max') {
                $maxLength = (int) $paramValue;
            }
        }

        self::length($value, $fieldName, $minLength, $maxLength);
    }

    private static function length($value, $fieldName, $minLength = null, $maxLength = null)
    {
        $length = strlen($value);

        if ($minLength !== null && $length < $minLength) {
            throw new Exception("O campo '$fieldName' deve ter pelo menos $minLength caracteres.", HttpExceptionConstants::BAD_REQUEST_CODE);
        }

        if ($maxLength !== null && $length > $maxLength) {
            throw new Exception("O campo '$fieldName' deve ter no máximo $maxLength caracteres.", HttpExceptionConstants::BAD_REQUEST_CODE);
        }
    }

    public static function validateDateTimeFormat($dateTime, $fieldName, $expectedFormat = 'Y-m-d H:i:s') {
        if(!empty($dateTime)) {
            $dateTimeObject = DateTime::createFromFormat($expectedFormat, $dateTime);

            if (!$dateTimeObject || !$dateTimeObject->format($expectedFormat) === $dateTime) {
                throw new Exception("O campo '$fieldName' deve estar no formato de data {$expectedFormat}.", HttpExceptionConstants::BAD_REQUEST_CODE);
            }
        }
    }
}