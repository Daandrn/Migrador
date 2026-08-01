<?php 

namespace App\Service;

use App\DTO\Client\InsertClientDto;
use App\DTO\Client\UpdateClientDto;
use App\Exceptions\EmptyClientsException;
use App\Exceptions\NotReadOnlyPermissionException;
use App\Models\Client;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function __construct(
        protected Client $client,
    ) {
        //
    }

    public function get(bool $onlyActives): array
    {
        return $this->client
            ->when(!empty($onlyActives), function ($query) {
                $query->where('active', true);
            })
            ->get()
            ->toArray();
    }

    public function store(InsertClientDto $dto)
    {
        return $this->client->create(
            $dto->toArray()
        );
    }

    public function update(UpdateClientDto $dto, int $id): bool
    {
        $client = $this->client->findOrFail($id);
        
        return $client->update($dto->toArray());
    }

    public function delete(int $id): bool
    {
        $client = $this->client->findOrFail($id);
        
        return $client->delete();
    }

    protected function clientConfigConnection(Client $client): void
    {
        Config::set('database.connections.origem', [
            'driver'   => $client->driver,
            'host'     => $client->host,
            'port'     => $client->port,
            'database' => $client->db_name,
            'username' => $client->user,
            'password' => $client->password,
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ]);

        return;
    }

    protected function ReadOnlyVerify(Connection $connection, ?string $userName = null): void
    {
        $result = $connection->selectOne(<<<SQL
            SELECT
                r.rolsuper
                OR r.rolcreatedb
                OR r.rolcreaterole
                OR r.rolreplication
                OR r.rolbypassrls
                OR has_database_privilege(
                    current_user,
                    current_database(),
                    'CREATE'
                )
                OR EXISTS (
                    SELECT 1
                    FROM pg_class c
                    JOIN pg_namespace n
                        ON n.oid = c.relnamespace
                    WHERE n.nspname NOT IN (
                        'pg_catalog',
                        'information_schema'
                    )
                    AND c.relkind IN ('r', 'p', 'v', 'm', 'f')
                    AND (
                        has_table_privilege(current_user, c.oid, 'INSERT')
                        OR has_table_privilege(current_user, c.oid, 'UPDATE')
                        OR has_table_privilege(current_user, c.oid, 'DELETE')
                        OR has_table_privilege(current_user, c.oid, 'TRUNCATE')
                        OR has_table_privilege(current_user, c.oid, 'TRIGGER')
                    )
                ) AS not_read_only
            FROM pg_roles r
            WHERE r.rolname = current_user
        SQL);

        $notReadOnly = (bool) $result->not_read_only;

        if ($notReadOnly) {
            throw new NotReadOnlyPermissionException(
                empty($userName)
                    ? "Este usuário possui permissões de escrita, por segurança não pode ser usado."
                    : "O usuário {$userName} possui permissões de escrita, por segurança não pode ser usado."
            );
        }
    }

    public function clientConnection(int $id): Connection
    {
        $client = $this->client->find($id);
        
        if (empty($client)) {
            throw new EmptyClientsException('Não há cliente cadastrado para os parametros. Verique!');
        }

        $this->clientConfigConnection(client: $client);
        
        DB::purge(name: 'origem');
        DB::reconnect(name: 'origem');

        $connection = DB::connection(
           name: 'origem'
        );

        $this->ReadOnlyVerify(
            connection: $connection,
            userName: $client->user
        );
        
        return $connection;
    }
}
