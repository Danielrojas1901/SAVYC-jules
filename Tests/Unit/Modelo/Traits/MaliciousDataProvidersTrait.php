<?php

namespace Tests\Unit\Modelo\Traits;

trait MaliciousDataProvidersTrait
{
    /**
     * DataProvider para casos maliciosos de seguridad
     * Reutilizable en todos los modelos para evitar duplicación de código
     */
    public static function casosMaliciosos(): array
    {
        return [
            'sql injection' => ["'; DROP TABLE tabla; --"],
            'xss script' => ['<script>alert("xss")</script>'],
            'html tags' => ['<b>negrita</b> <i>cursiva</i>'],
            'null bytes' => ["\0\0\0"],
            'control characters' => ["\x00\x01\x02\x03\x04\x05"],
            'backslash escape' => ['\\n\\t\\r'],
            'unicode null' => ["\u0000"],
            'bom' => ["\xEF\xBB\xBF"],
            'zero width' => ["\u200B\u200C\u200D"],
            'right to left' => ["\u202E"],
            'invisible chars' => ["\u2060\u2061\u2062"],
            'very long line' => [str_repeat('a', 1000)],
            'mixed nulls' => ["a\0b\0c\0"],
            'json injection' => ['{"malicious": "data"}'],
            'xml injection' => ['<xml><data>test</data></xml>'],
            'path traversal' => ['../../../etc/passwd'],
            'command injection' => ['; rm -rf /'],
            'php code' => ['<?php echo "hack"; ?>'],
            'javascript' => ['javascript:alert(1)'],
            'data uri' => ['data:text/html,<script>alert(1)</script>'],
        ];
    }

    /**
     * DataProvider para tipos de datos problemáticos
     * Reutilizable en todos los modelos para evitar duplicación de código
     */
    public static function tiposDatosProblematicos(): array
    {
        return [
            'array vacio' => [[]],
            'object vacio' => [new \stdClass()],
            'array con datos' => [['key' => 'value']],
            'object con propiedades' => [(object)['key' => 'value']],
            'resource' => [fopen('php://memory', 'r')],
            'callable' => [function() { return 'test'; }],
            'float' => [123.45],
            'integer' => [123],
            'array numerico' => [[1, 2, 3]],
            'array asociativo' => [['a' => 1, 'b' => 2]],
        ];
    }

    /**
     * DataProvider para casos edge de longitud
     * Reutilizable para campos con validación de longitud
     */
    public static function casosEdgeLongitud(int $min = 2, int $max = 50): array
    {
        return [
            'minimo exacto' => [str_repeat('a', $min)],
            'minimo mas uno' => [str_repeat('a', $min + 1)],
            'maximo exacto' => [str_repeat('a', $max)],
            'maximo mas uno' => [str_repeat('a', $max + 1)],
            'maximo mas diez' => [str_repeat('a', $max + 10)],
            'maximo mas cien' => [str_repeat('a', $max + 100)],
            'un caracter' => ['a'],
            'cero caracteres' => [''],
            'solo espacios' => ['  '],
            'solo espacios minimo' => [str_repeat(' ', $min)],
            'solo espacios maximo' => [str_repeat(' ', $max)],
            'solo espacios mas uno' => [str_repeat(' ', $max + 1)],
            'tabs' => ["\t\t"],
            'newlines' => ["\n\n"],
            'carriage return' => ["\r\r"],
            'mezcla espacios' => [" \t\n\r "],
            'unicode' => ['áéíóú'],
            'unicode maximo' => [str_repeat('á', $max)],
            'unicode mas uno' => [str_repeat('á', $max + 1)],
            'emojis' => ['😀😀'],
            'emojis maximo' => [str_repeat('😀', intval($max / 2))],
            'emojis mas uno' => [str_repeat('😀', intval($max / 2) + 1)],
        ];
    }

    /**
     * DataProvider para caracteres especiales
     * Reutilizable para campos que permiten caracteres especiales
     */
    public static function caracteresEspeciales(): array
    {
        return [
            'con parentesis' => ['Texto (Especial)'],
            'con corchetes' => ['Texto [2024]'],
            'con llaves' => ['Texto {Premium}'],
            'con arroba' => ['Texto @empresa'],
            'con porcentaje' => ['Texto 100%'],
            'con dolar' => ['Texto $5000'],
            'con ampersand' => ['Texto & productos'],
            'con asterisco' => ['Texto * especial'],
            'con mas' => ['Texto + productos'],
            'con igual' => ['Texto = productos'],
            'con exclamacion' => ['Texto! urgente'],
            'con interrogacion' => ['Texto? dudoso'],
            'con comillas' => ['Texto "especial"'],
            'con apostrofe' => ["Texto 'varios'"],
            'con barra' => ['Texto / productos'],
            'con backslash' => ['Texto \\ productos'],
            'con pipe' => ['Texto | productos'],
            'con tilde' => ['Texto ~ productos'],
            'con acento grave' => ['Texto ` productos'],
            'con simbolos multiples' => ['Texto #123 - Productos (50) @empresa $1000'],
        ];
    }

}
