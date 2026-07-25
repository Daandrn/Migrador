<?php 

namespace App\Contracts;

interface ErrorRepositoryInterface
{
    public function gravar(array $data): bool;
    public function buscar(?array $types): array;
}
