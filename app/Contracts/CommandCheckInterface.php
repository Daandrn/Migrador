<?php 

namespace App\Contracts;

use App\Models\Client;

interface CommandCheckInterface
{
    public function add(string $check);
    public function execute(int $clientId);
}
