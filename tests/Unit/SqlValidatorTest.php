<?php

namespace Tests\Unit;

use App\Exceptions\MultipleQueryException;
use App\Exceptions\UnsafeSqlException;
use App\Helpers\SqlValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SqlValidatorTest extends TestCase
{
    #[DataProvider('validSqlProvider')]
    public function test_it_accepts_read_only_statements(string $sql): void
    {
        $validator = new SqlValidator();

        $this->assertNotSame('', $validator->make($sql));
    }

    public function test_it_rejects_multiple_statements(): void
    {
        $validator = new SqlValidator();

        $this->expectException(MultipleQueryException::class);

        $validator->make('SELECT 1; SELECT 2;');
    }

    #[DataProvider('unsafeSqlProvider')]
    public function test_it_rejects_unsafe_statements(string $sql): void
    {
        $validator = new SqlValidator();

        $this->expectException(UnsafeSqlException::class);

        $validator->make($sql);
    }

    public static function validSqlProvider(): array
    {
        return [
            'select' => ['SELECT * FROM compras.pcmater'],
            'with select' => ['WITH materiais AS (SELECT * FROM compras.pcmater) SELECT * FROM materiais'],
            'semicolon inside literal' => ["SELECT 'texto; interno' AS texto"],
        ];
    }

    public static function unsafeSqlProvider(): array
    {
        return [
            'insert' => ['INSERT INTO compras.pcmater (pc01_codmater) VALUES (1)'],
            'update' => ['UPDATE compras.pcmater SET pc01_descrmater = NULL'],
            'delete' => ['DELETE FROM compras.pcmater'],
            'write cte' => ['WITH removidos AS (DELETE FROM compras.pcmater RETURNING *) SELECT * FROM removidos'],
        ];
    }
}
