<?php 

namespace App\DTO\Client;

use App\Http\Requests\Api\Client\UpdateClientRequest;
use Illuminate\Support\Facades\Crypt;

readonly class UpdateClientDto
{
    public function __construct(
        public int    $id,
        public string $host,
        public int    $port,
        public string $user,
        public ?string $password,
        public string $db_name,
        public string $driver,
        public bool   $active,
    ) {
        //
    }

    public static function make(UpdateClientRequest $request): self
    {
        return new self(
            id: $request->id,
            host: $request->host,
            port: $request->port,
            user: $request->user,
            password: $request->password,
            db_name: $request->db_name,
            driver: $request->driver,
            active: $request->active,
        );
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'host'     => $this->host,
            'port'     => $this->port,
            'user'     => $this->user,
            'password' => $this->password,
            'db_name'  => $this->db_name,
            'driver'   => $this->driver,
            'active'   => $this->active,
        ];
    }
}
