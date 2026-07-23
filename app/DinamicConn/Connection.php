<?php 

namespace App\DinamicConn;

use App\Models\Clientes;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Connection
{
    public function make(Clientes $cliente): void
    {
        Config::set('database.connections.origem', [
            'driver' => $cliente->driver,
            'host' => $cliente->host,
            'port' => $cliente->porta,
            'database' => $cliente->nome_banco,
            'username' => $cliente->usuario,
            'password' => $cliente->senha,
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ]);

        DB::purge('origem');
        DB::reconnect('origem');
    }
}
