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

    public function create(object|array $data)
    {
        return $this->error->create([
            'data' => json_encode($data),
            'type_id' => 1,
        ]);
    }
    
    public function get(?array $types): array
    {
        $query = $this->error
            ->query()
            ->join('verify_types', 'errors.type_id', '=', 'verify_types.id')
            ->when(!empty($types), function ($query) use ($types) {
                $query->whereIn('type_id', $types);
            });
        
        return $query->get(
                ['errors.id', 'errors.data', 'errors.type_id', 'verify_types.description']
            )
            ->toArray();
    }
}
