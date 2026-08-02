<?php 

namespace App\Repositories;

use App\DTO\Check\{
    CheckDto,
    UpdateCheckDto,
    InsertCheckDto,
};
use App\Models\Check;
use App\Types\SqlQuery;

class CheckRepository
{
    public function __construct(
        protected Check $check,
    ) {
        //
    }

    public function getChecks(?array $types): array
    {
        $checks = $this->check
            ->select([
                'checks.id',
                'checks.description',
                'checks.type_id',
                'checks.sql_query',
                'checks.active',
                'verify_types.description as description_type',
            ])
            ->join('verify_types', 'checks.type_id', '=', 'verify_types.id')
            ->when(!empty($types), function ($query) use ($types) {
                $query->whereIn('type_id', $types);
            })
            ->orderBy('checks.type_id', 'asc')
            ->orderBy('checks.id', 'desc')
            ->get();
        
        return $checks->toArray();
    }

    public function getById(array $ids): array
    {
        $checks = $this->check
            ->whereIn('id', $ids)
            ->get();
        
        return $checks->toArray();
    }

    public function create(InsertCheckDto $data): CheckDto
    {
        $createdCheck = $this->check->create($data->toArray());
        
        return CheckDto::make(
            id: $createdCheck->id,
            description: $createdCheck->description,
            type_id: $createdCheck->type_id,
            sql_query: new SqlQuery($createdCheck->sql_query),
            active: $createdCheck->active,
        );
    }

    public function update(UpdateCheckDto $data): bool
    {
        $check = $this->check->findOrFail($data->id);
        
        return $check->update($data->toArray());
    }

    public function delete(int $id): bool
    {
        $check = $this->check->findOrFail($id);
        
        return $check->delete();
    }
}
