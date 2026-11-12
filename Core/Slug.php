<?php

namespace Core;

/**
 * Convierte un texto en un slug apto para URL: minúsculas, sin acentos y con
 * guiones. Los acentos se transcriben con una tabla propia en lugar de iconv,
 * porque iconv//TRANSLIT depende de la locale y da resultados distintos en
 * Windows y en el servidor Linux.
 */
class Slug
{
    private const MAPA = [
        'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a',
        'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
        'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o',
        'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
        'ñ' => 'n', 'ç' => 'c',
    ];

    public static function make(string $texto): string
    {
        $texto = mb_strtolower(trim($texto), 'UTF-8');
        $texto = strtr($texto, self::MAPA);
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
        return trim($texto, '-');
    }
}
