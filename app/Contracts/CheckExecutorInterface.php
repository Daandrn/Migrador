<?php 

namespace App\Contracts;

use App\Models\Check;
use App\Models\Client;

interface CheckExecutorInterface
{

    /**
     * @var Check[] $checks
     */
    public function run(Client $client, array $checks): void;
}
