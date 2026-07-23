<?php

namespace Tests\Unit;

use App\Helpers\NormalizeSql;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SqlNormalizerTest extends TestCase
{
    /**
     * @dataProvider normalizeProvider
     */
    #[DataProvider('normalizeProvider')]
    public function testNormalize(string $sql, string $expected): void
    {     
        $this->assertSame($expected, (NormalizeSql::make($sql)));
    }

    public static function normalizeProvider(): array
    {
        return [
            'sql simples' => [
                'SELECT * FROM usuarios',
                'SELECT * FROM usuarios',
            ],

            'remove espaços duplicados' => [
                'SELECT    *    FROM     usuarios',
                'SELECT * FROM usuarios',
            ],

            'remove tabs' => [
                "SELECT\t*\tFROM\tusuarios",
                'SELECT * FROM usuarios',
            ],

            'remove quebras de linha' => [
                "SELECT\n*\nFROM\nusuarios",
                'SELECT * FROM usuarios',
            ],

            'remove mistura de espaços tabs e quebras' => [
                "SELECT \n\t * \t \n FROM   usuarios",
                'SELECT * FROM usuarios',
            ],

            'remove espaços das extremidades' => [
                "   SELECT * FROM usuarios   ",
                'SELECT * FROM usuarios',
            ],

            'where complexo' => [
                "
                    SELECT
                        id,
                        nome
                    FROM
                        usuarios
                    WHERE
                        ativo = true
                        AND idade > 18
                ",
                'SELECT id, nome FROM usuarios WHERE ativo = true AND idade > 18',
            ],

            'string literal preservada' => [
                "SELECT * FROM usuarios WHERE nome = 'João da Silva'",
                "SELECT * FROM usuarios WHERE nome = 'João da Silva'",
            ],

            'sql vazio' => [
                '',
                '',
            ],

            'apenas espaços' => [
                " \n\t  ",
                '',
            ],
        ];
    }
}
