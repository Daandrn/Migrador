<?php 

namespace App\DTO\Check;

use App\Http\Requests\Api\Check\UpdateCheckRequest;
use App\Types\SqlQuery;

readonly class UpdateCheckDto
{
    public function __construct(
        public int    $id,
        public string $description,
        public int    $type_id,
        public SqlQuery $sql_query,
        public bool   $active,
    ) {
        //
    }

    public static function make(UpdateCheckRequest $request): self
    {
        return new self(
            id: $request->id,
            description: $request->description,
            type_id: $request->type_id,
            sql_query: new SqlQuery($request->sql_query),
            active: $request->active,
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
