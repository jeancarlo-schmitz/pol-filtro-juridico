<?php

namespace Infrastructure\Utils;

class Sanitizer
{
    public function sanitizeAll($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeValue($value);
            }
        }else{
            $data = $this->sanitize($data);
        }

        return $data;
    }

    private function sanitizeValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $subKey => $subValue) {
                $value[$subKey] = $this->sanitizeValue($subValue);
            }
        } else {
            $value = $this->sanitize($value);
        }

        return $value;
    }

    private function sanitize($value)
    {
        $value = trim($value);
        $value = stripslashes($value);
        $value = filter_var($value, FILTER_SANITIZE_STRING);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $value = EncodingConverter::convertNestedArrayToIso88591($value);
        $value = $this->spaceReservedWords($value);

        return $value;
    }

    private function spaceReservedWords($value) {
        $databaseReservedWorkd = ['SELECT', 'FROM', 'WHERE', 'JOIN'];
        $phpReservedWorkd = ['<?php', '<?', 'if', 'else'];

        $value = $this->spaceDataBaseReservedWords($databaseReservedWorkd, $value);
        $value = $this->spacePhpReservedWords($phpReservedWorkd, $value);

        return $value;
    }

    private function spaceDataBaseReservedWords($databaseReservedWorkd, $value){
        $pattern = '/\b('.implode('|', $databaseReservedWorkd).')\b/iu';
        $output = preg_replace_callback($pattern, function ($matches) {
            return preg_replace('/\w{2}/u', '$0 ', $matches[0], 1);
        }, $value);

        return $output;
    }

    private function spacePhpReservedWords($databaseReservedWorkd, $value){
        $pattern = '/\b('.implode('|', $databaseReservedWorkd).')\b/iu';
        $output = preg_replace_callback($pattern, function ($matches) {
            return preg_replace('/\w{1}/u', '$0 ', $matches[0], 1);
        }, $value);

        return $output;
    }
}