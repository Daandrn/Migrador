<?php 

namespace App\Contracts;

interface CommandCheckInterface
{
    public function add(string $check);
    public function execute();

    /**
     * @param string[] $data
     */
    public function getErrors(?array $data);
}
