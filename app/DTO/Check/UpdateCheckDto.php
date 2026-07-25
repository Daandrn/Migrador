<?php 

namespace App\DTO\Check;

use App\Http\Requests\Api\Check\UpdateCheckRequest;

readonly class UpdateCheckDto
{
    public function __construct(
        public int    $id,
        public string $description,
        public int    $type_id,
        public string $sql_query,
        public bool   $active,
    ) {
        //
    }

    public static function make(UpdateCheckRequest $request): self
    {
        return new self(
            $request->id,
            $request->description,
            $request->type_id,
            $request->sql_query,
            $request->active,
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'description' => $this->description,
            'type_id'     => $this->type_id,
            'sql_query'   => $this->sql_query,
            'active'      => $this->active,
        ];
    }
}
