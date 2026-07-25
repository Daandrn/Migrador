<?php 

namespace App\DTO\Check;

use App\Http\Requests\Api\Check\StoreCheckRequest;

readonly class InsertCheckDto
{
    public function __construct(
        public string $description,
        public int    $type_id,
        public string $sql_query,
        public bool   $active,
    ) {
        //
    }

    public static function make(StoreCheckRequest $request): self
    {
        return new self(
            $request->description,
            $request->type_id,
            $request->sql_query,
            $request->active,
        );
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'type_id'     => $this->type_id,
            'sql_query'   => $this->sql_query,
            'active'      => $this->active,
        ];
    }
}
