<?php 

namespace App\Contracts;

interface VerifyErrorRepositoryInterface
{
    public function create(object|array $data, int $type);
    public function get(?array $types): array;
    public function delete(array $ids): int;
}
