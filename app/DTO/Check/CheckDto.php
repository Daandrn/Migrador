<?php 

namespace App\DTO\Check;

readonly class CheckDto
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

    public static function make(
        int    $id,
        string $description,
        int    $type_id,
        string $sql_query,
        bool   $active,
    ): self 
    {
        return new self(
            id: $id,
            description: $description,
            type_id: $type_id,
            sql_query: $sql_query,
            active: $active,
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
