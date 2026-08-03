<?php 

namespace App\Contracts;

use App\Models\Client;
use App\Types\SqlQuery;

interface CheckExecutorInterface
{

    /**
     * @var SqlQuery[] $checks
     */
    public function run(Client $client, array $checks): void;
}
