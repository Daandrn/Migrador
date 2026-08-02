<?php

namespace App\Helpers;

use App\Exceptions\{
    InvalidQuerySintaxException,
    MultipleQueryException,
    UnsafeSqlException,
};
use App\Helpers\NormalizeSql;
use App\Traits\BlockedActions;
use Kodus\SQLSplit\Splitter;

class SqlValidator
{
    use BlockedActions;

    public static function make(string $sql): string
    {
        $sql = NormalizeSql::make($sql);

        if ($sql === '') {
            throw new UnsafeSqlException('A consulta SQL não pode estar vazia.');
        }

        try {
            $statements = array_values(array_filter(
                Splitter::split(sql: $sql, strip_comments: true),
                static fn (string $statement): bool => trim($statement) !== '',
            ));
        } catch (\Throwable $error) {
            $message = strtolower($error->getMessage());

            $invalidQuerySintax = str_contains($message, 'expected token or group end');

            $messageInfo = match (true) {
                $invalidQuerySintax => 'A sintaxe do SQL está inválida, verifique!',
                default => $error->getMessage()
            };

            throw new InvalidQuerySintaxException($messageInfo);
        }

        if (count($statements) !== 1) {
            throw new MultipleQueryException(
                'Apenas uma instrução SQL é permitida.'
            );
        }

        $statement = trim($statements[0]);

        if (!preg_match('/^(SELECT|WITH)\b/i', $statement)) {
            throw new UnsafeSqlException(
                'Apenas consultas iniciadas por SELECT ou WITH são permitidas.'
            );
        }

        foreach (self::blockedActions() as $action) {
            if (preg_match('/\b'.preg_quote($action, '/').'\b/i', $statement)) {
                throw new UnsafeSqlException(
                    "A instrução {$action} não é permitida em uma checagem."
                );
            }
        }

        return $statement;
    }
}
