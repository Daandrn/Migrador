<?php 

namespace App\DinamicConn;

use App\Models\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Connection
{
    public function make(Client $client): void
    {
        Config::set('database.connections.origem', [
            'driver' => $client->driver,
            'host' => $client->host,
            'port' => $client->port,
            'database' => $client->db_name,
            'username' => $client->user,
            'password' => $client->password,
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ]);

        DB::purge('origem');
        DB::reconnect('origem');
    }
}
