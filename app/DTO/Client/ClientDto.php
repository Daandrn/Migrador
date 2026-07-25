<?php 

namespace App\DTO\Client;

readonly class ClientDto
{
    public function __construct(
        public int    $id,
        public string $host,
        public int    $port,
        public string $user,
        public string $password,
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
        string $password,
        string $db_name,
        string $driver,
        bool   $active,
    ): self
    {
        return new self(
            $id,
            $host,
            $port,
            $user,
            $password,
            $db_name,
            $driver,
            $active,
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
