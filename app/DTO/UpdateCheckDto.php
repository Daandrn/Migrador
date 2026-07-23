<?php 

namespace App\DTO;

use App\Http\Requests\Api\Checks\UpdateCheckRequest;

readonly class UpdateCheckDto
{
    public function __construct(
        public int $id,
        public string $descricao,
        public int $tipo_id,
        public string $consulta_sql,
        public bool   $ativo,
    ) {
        //
    }

    public static function make(UpdateCheckRequest $request): self
    {
        return new self(
            $request->id,
            $request->descricao,
            $request->tipo_id,
            $request->consulta_sql,
            $request->ativo,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'descricao' => $this->descricao,
            'tipo_id' => $this->tipo_id,
            'consulta_sql' => $this->consulta_sql,
            'ativo' => $this->ativo,
        ];
    }
}
