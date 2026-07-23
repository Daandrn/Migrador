<?php 

namespace App\DTO;

use App\Http\Requests\Api\Checks\StoreCheckRequest;

readonly class InsertCheckDto
{
    public function __construct(
        public string $descricao,
        public int $tipo_id,
        public string $consulta_sql,
        public bool   $ativo,
    ) {
        //
    }

    public static function make(StoreCheckRequest $request): self
    {
        return new self(
            $request->descricao,
            $request->tipo_id,
            $request->consulta_sql,
            $request->ativo,
        );
    }

    public function toArray(): array
    {
        return [
            'descricao' => $this->descricao,
            'tipo_id' => $this->tipo_id,
            'consulta_sql' => $this->consulta_sql,
            'ativo' => $this->ativo,
        ];
    }
}
