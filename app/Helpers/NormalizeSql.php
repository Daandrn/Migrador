<?php 

namespace App\Helpers;

class NormalizeSql
{
    /**
     * Remove espaços e quebras de linha do texto do sql.
     */
    public static function make(string $sql): string
    {
        return trim(preg_replace('/\s+/', ' ', $sql));
    }
}
