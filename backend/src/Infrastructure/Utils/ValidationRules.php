<?php

namespace Infrastructure\Utils;

use InvalidArgumentException;

class ValidationRules
{
    const NOT_EMPTY = 'not_empty';
    const LENGTH = 'length';
    const MUST_BE_INT = 'must_be_int';
    const VALIDATE_DATE_TIME_FORMAT = 'validate_date_time_format';

    public static function notEmpty()
    {
        return 'not_empty';
    }

    public static function mustBeInt()
    {
        return 'must_be_int';
    }

    public static function length(int $min = null, int $max = null)
    {
        if ($min === null && $max === null) {
            throw new InvalidArgumentException('Pelo menos o parтmetro min ou max deve ser fornecido para a regra de validaчуo length');
        }

        $rule = 'length';

        if ($min !== null) {
            $rule .= "::min=$min";
        }

        if ($max !== null) {
            $rule .= "::max=$max";
        }

        return $rule;
    }

    public static function validateDateTimeFormat($expectedFormat = 'Y-m-d H:i:s') {
        return "validate_date_time_format::{$expectedFormat}";
    }

}