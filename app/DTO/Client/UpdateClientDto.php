<?php 

namespace App\DTO\Client;

use App\Http\Requests\Api\Client\UpdateClientRequest;

readonly class UpdateClientDto
{
    public function __construct(
        public int    $id,
        public int    $port,
        public string $user,
        public string $password,
        public string $db_name,
        public string $driver,
        public bool   $active,
    ) {
        //
    }

    public static function make(UpdateClientRequest $request): self
    {
        return new self(
            $request->id,
            $request->port,
            $request->user,
            $request->password,
            $request->db_name,
            $request->driver,
            $request->active,
        );
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'port'     => $this->port,
            'user'     => $this->user,
            'password' => $this->password,
            'db_name'  => $this->db_name,
            'driver'   => $this->driver,
            'active'   => $this->active,
        ];
    }
}
