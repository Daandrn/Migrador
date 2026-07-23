<?php 

namespace App\Contracts;

interface CommandCheckInterface
{
    public function adicionar(string $check);
    public function executar();

    /**
     * @param string[] $data
     */
    public function buscarErros(?array $data);
}
