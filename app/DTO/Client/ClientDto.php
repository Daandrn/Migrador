<?php 

namespace App\DTO\Client;

readonly class ClientDto
{
    public function __construct(
        public int    $id,
        public string $host,
        public int    $port,
        public string $user,
        public string $db_name,
        public string $driver,
        public bool   $active,
    ) {
        //
    }

    public static function make(
        int    $id,
        string $host,
        int    $port,
        string $user,
        string $db_name,
        string $driver,
        bool   $active,
    ): self
    {
        return new self(
            id: $id,
            host: $host,
            port: $port,
            user: $user,
            db_name: $db_name,
            driver: $driver,
            active: $active,
        );
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'host'     => $this->host,
            'port'     => $this->port,
            'user'     => $this->user,
            'db_name'  => $this->db_name,
            'driver'   => $this->driver,
            'active'   => $this->active,
        ];
    }
}
