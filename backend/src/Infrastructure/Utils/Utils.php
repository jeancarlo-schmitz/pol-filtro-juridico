<?php

namespace Infrastructure\Utils;

class Utils
{
    static function isDev()
    {
        if (PHP_OS == "WINNT" || strpos(php_uname(), ".dev") !== false
            || strpos(php_uname(), "poldev") !== false
            || strpos(php_uname(), "localhost") !== false
        ) {
            return true;
        }

        return false;
    }

    public static function ajustarMascaraNumeroProcessoCNJ($numeroProcesso)
    {
        // Remove qualquer caractere que não seja número
        $numeroProcesso = preg_replace('/\D/', '', $numeroProcesso);

        $numeroProcessoFormatado = preg_replace(
            '/(\d{7})(\d{2})(\d{4})(\d{1})(\d{4})/',
            '$1-$2.$3.$4.$5',
            $numeroProcesso
        );

        return $numeroProcessoFormatado;
    }

    public static function getIpAddr()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // IP transmitido pelo proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // IP real do cliente
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public static function explodeString($input, $separators)
    {
        if (!empty($input)) {
            if (is_array($separators)) {
                $pattern = implode('|', array_map('preg_quote', $separators));
                $input = preg_replace('/' . $pattern . '/', '|', $input);
                $parts = explode('|', $input);
            } else {
                $parts = explode($separators, $input);
            }

            $trimmedParts = [];
            foreach ($parts as $part) {
                $trimmedPart = trim($part);
                if ($trimmedPart !== '') {
                    $trimmedParts[] = $trimmedPart;
                }
            }
            return $trimmedParts;
        }

        return [];
    }

    public static function extractDate(string $input)
    {
        $pattern = '/(\d{2}\/\d{2}\/\d{4})/'; // Padrao para encontrar a data no formato dd/mm/yyyy
        if (preg_match($pattern, $input, $matches)) {
            return $matches[0];
        }
        return null; // Retorna null se não encontrar a data
    }

    static function isBooleanEqualsTrue($boolean){
        return $boolean === true || $boolean === 'true' || $boolean === 1 || $boolean === 't';
    }

    static function isBooleanEqualsFalse($boolean){
        return $boolean === false || $boolean === 'false' || $boolean === 0 || $boolean === 'f';
    }
}