<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Clientes\{
    UpdateClientesRequest,
    StoreClientesRequest,
};
use App\Models\Clientes;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class ClientesController extends Controller
{
    public function __construct(
        protected Clientes $clientes,
    ) {
        //
    }   
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {  
        $clientes = $this->clientes->get();

        return response()
            ->json($clientes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Clientes $clientes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clientes $clientes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientesRequest $request, int $id)
    {
        $cliente = $this->clientes->findOrFail($id);
        
        $cliente->update($request->all());
        
        return response()
            ->json($cliente);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $cliente = $this->clientes->findOrFail($id);
        
        $cliente->delete();

        return response()
            ->noContent();
    }

    public static function clienteConfigConnection(Clientes $clientes): void
    {
        $cliente = $clientes->query()
            ->where('id', 1)
            ->first();

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

        return;
    }

    public static function clienteConnection(Clientes $cliente): Connection
    {
        DB::purge('origem');
        DB::reconnect('origem');
        
        return DB::connection(
           'origem'
        );
    }
}
