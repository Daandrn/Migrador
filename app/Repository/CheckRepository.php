<?php 

namespace App\Repository;

use App\DTO\Check\CheckDto;
use App\DTO\Check\InsertCheckDto;
use App\DTO\Check\UpdateCheckDto;
use App\Models\Check;

class CheckRepository
{
    public function __construct(
        protected Check $check,
    ) {
        //
    }

    public function create(InsertCheckDto $data): CheckDto
    {
        $createdCheck = $this->check->create($data->toArray());
        
        return CheckDto::make(
            $createdCheck->id,
            $createdCheck->description,
            $createdCheck->type_id,
            $createdCheck->sql_query,
            $createdCheck->active,
        );
    }

    public function update(UpdateCheckDto $data): bool
    {
        $check = $this->check->findOrFail($data->id);
        
        return $check->update($data->toArray());
    }

    public function getChecks(?array $types): array
    {
        $check = $this->check
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
            ->orderBy('checks.id', 'desc');
        
        return $check->get()
            ->toArray();
    }

    public function delete(int $id)
    {
        $check = $this->check->findOrFail($id);
        
        return $check->delete();
    }
}
