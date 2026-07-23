<?php 

namespace App\Repository;

use App\DTO\InsertCheckDto;
use App\DTO\UpdateCheckDto;
use App\Models\Check;

class CheckRepository
{
    public function __construct(
        protected Check $check,
    ) {
        //
    }

    public function insert(InsertCheckDto $data): bool
    {
        return $this->check->save($data->toArray());
    }

    public function update(UpdateCheckDto $data): bool
    {
        $check = $this->check->findOrFail($data->id);
        
        return $check->update($data->toArray());
    }

    public function buscar(?array $tipos): array
    {
        $query = $this->check
            ->query()
            ->join('verify_types', 'checks.tipo_id', '=', 'verify_types.id')
            ->when(!empty($tipos), function ($query) use ($tipos) {
                $query->whereIn('tipo_id', $tipos);
            });
        
        return $query->get(
                ['checks.id', 'checks.descricao', 'checks.tipo_id', 'checks.consulta_sql', 'checks.ativo', 'verify_types.descricao as descricao_tipo']
            )
            ->toArray();
    }

    public function delete(int $id)
    {
        $check = $this->check->findOrFail($id);
        
        return $check->delete();
    }
}
