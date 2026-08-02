<?php 

namespace App\Services;

use App\DTO\Client\InsertClientDto;
use App\DTO\Client\UpdateClientDto;
use App\Exceptions\EmptyClientsException;
use App\Exceptions\NotReadOnlyPermissionException;
use App\Models\Client;
use Exception;
use Illuminate\Database\Connection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function __construct(
        protected Client $client,
    ) {
        //
    }

    public function get(?bool $onlyActives = null): array
    {
        $clients = $this->client
            ->when($onlyActives, function ($query) {
                $query->where('active', true);
            })
            ->get()
            ->makeHidden('password');
        
        return $clients->toArray();
    }

    public function find(int $id): Client
    {
        $client = $this->client->find($id);

        if (empty($client)) {
            throw new EmptyClientsException('Não existem clientes cadastrados para os parametros!');
        };
        
        return $client;
    }

    public function store(InsertClientDto $dto)
    {
        $data = $dto->toArray();
        $data['password'] = Crypt::encryptString($dto->password);
        
        return $this->client->create(
           $data
        );
    }

    public function update(UpdateClientDto $dto, int $id): Client
    {
        $client = $this->client->findOrFail($id);
        $data = $dto->toArray();

        if (empty($dto->password)) {
            unset($data['password']);
        } else {
            $data['password'] = Crypt::encryptString($data['password']);
        }

        $client->update(
            $data
        );
        
        return $client;
    }

    public function delete(int $id): bool
    {
        $client = $this->client->findOrFail($id);
        
        return $client->delete();
    }

    protected function configConnection(Client $client): void
    {
        Config::set('database.connections.origem', [
            'driver'   => $client->driver,
            'host'     => $client->host,
            'port'     => $client->port,
            'database' => $client->db_name,
            'username' => $client->user,
            'password' => Crypt::decryptString($client->password),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ]);

        return;
    }

    protected function connectionReadonlyVerify(Connection $connection): void
    {
        $readOnlyState = $connection->selectOne(
            'SHOW default_transaction_read_only'
        );

        if (
            isset($readOnlyState->default_transaction_read_only) 
            && $readOnlyState->default_transaction_read_only === 'on'
        ) {
            return;
        }

        throw new NotReadOnlyPermissionException("O modo de acesso não está configurado como \"Apenas leitura\", por segurança essa conexão não poderá ser usada.");
    }

    protected function userReadOnlyVerify(Connection $connection, ?string $userName = null): void
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

        if (!$notReadOnly) {
            return;
        }

        throw new NotReadOnlyPermissionException(
            empty($userName)
                ? "Este usuário possui permissões de escrita, por segurança não pode ser usado."
                : "O usuário {$userName} possui permissões de escrita, por segurança não pode ser usado."
        );
    }

    public function validAndConnect(Client $client): Connection
    {
        $this->configConnection(client: $client);
        
        DB::purge(name: 'origem');
        DB::reconnect(name: 'origem');

        $connection = DB::connection(
           name: 'origem'
        );

        try {
            $connection->statement(
                'SET SESSION CHARACTERISTICS AS TRANSACTION READ ONLY'
            );

            $connection->selectOne('select 1;');
        } catch (QueryException $error) {
            $message = strtolower($error->getMessage());

            $unknownHost = str_contains($message, 'could not translate host name')
                || str_contains($message, 'Name or service not known');
            $invalidUserOrPassword = str_contains($message, 'password authentication failed for user');
            $unknowDatabase = str_contains($message, 'database') && str_contains($message, 'does not exist');

            $messageInfo = match (true) {
                $unknownHost => 'O host informado não foi encontrado. Verifique o endereço do servidor.',
                $invalidUserOrPassword => 'Usuário e/ou senha inválidos.',
                $unknowDatabase => 'Não existe base de dados com este nome.',
                default => $error->getMessage()
            };
            
            throw new Exception($messageInfo);
        }

        $this->connectionReadonlyVerify(
            connection: $connection,
        );

        $this->userReadOnlyVerify(
            connection: $connection,
            userName: $client->user,
        );
        
        return $connection;
    }
}
