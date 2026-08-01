<?php 

namespace App\Service;

use App\Exceptions\MultplyQueryException;
use App\Helpers\NormalizeSql;
use App\Repository\CheckRepository;
use Kodus\SQLSplit\Splitter;

class CheckService
{
    public function __construct() {
        //
    }

    public function verifyMultSql(string $sql)
    {
        $sql = NormalizeSql::make(sql: $sql);

        $queryCount = count(Splitter::split(sql: $sql, strip_comments: true));
        
        if ($queryCount > 1) {
            throw new MultplyQueryException('Apenas uma instrução SQL é permitida por Check.');
        }
    }
}
