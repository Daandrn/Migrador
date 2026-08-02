<?php 

namespace App\Repositories;

use App\Models\VerifyError;
use Exception;

class VerifyErrorRepository
{
    public function __construct(
        protected VerifyError $model,
    ) {
        //
    }

    public function create(object|array $data)
    {
        return $this->model->create([
            'data' => json_encode($data),
            'type_id' => 1,
        ]);
    }
    
    public function get(?array $types): array
    {
        $query = $this->model
            ->query()
            ->join('verify_types', 'verify_errors.type_id', '=', 'verify_types.id')
            ->when(!empty($types), function ($query) use ($types) {
                $query->whereIn('verify_errors.type_id', $types);
            })
            ->orderBy('id');
        
        return $query->get(
                ['verify_errors.id', 'verify_errors.data', 'verify_errors.type_id', 'verify_types.description as description_type']
            )
            ->toArray();
    }

    public function delete(array $ids): int
    {
        $quantityDeleted = $this->model->destroy($ids);

        $quantityForDelete = count($ids);
        
        if ($quantityForDelete !== $quantityDeleted) {
            throw new Exception("A quantidade de itens excluídos é diferente da quantidade de exclusões solicitadas. Excluídos: {$quantityDeleted}. Exclusões solicitadas: {$quantityForDelete}.");
        }
        
        return $quantityDeleted; 
    }
}
