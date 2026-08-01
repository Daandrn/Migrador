<?php 

namespace App\Contracts;

interface VerifyErrorRepositoryInterface
{
    public function gravar(array $data): bool;
    public function buscar(?array $types): array;
}
