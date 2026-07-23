<?php 

namespace App\Repository;

use App\Models\Error;

class ErrorRepository
{
    public function __construct(
        protected Error $error,
    ) {
        //
    }

    public function gravar(object|array $data)
    {
        return $this->error->create([
            'data' => json_encode($data),
            'tipo_id' => 1,
        ]);
    }
    
    public function buscar(?array $tipos): array
    {
        $query = $this->error
            ->query()
            ->join('verify_types', 'errors.tipo_id', '=', 'verify_types.id')
            ->when(!empty($tipos), function ($query) use ($tipos) {
                $query->whereIn('tipo_id', $tipos);
            });
        
        return $query->get(
                ['errors.id', 'errors.data', 'errors.tipo_id', 'verify_types.descricao']
            )
            ->toArray();
    }
}
