<?php

namespace App\Http\Controllers\Api;

use App\DTO\Client\InsertClientDto;
use App\Exceptions\EmptyClientsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Client\StoreClientRequest;
use App\Http\Requests\Api\Client\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Database\Connection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class ClientController extends Controller
{
    public function __construct(
        protected Client $client,
    ) {
        //
    }   
    
    /**
     * Display a listing of the resource.
     */
    public function getAll(): JsonResponse
    {
        $active = (bool) true;
        
        $client = $this->client
            ->when(!empty($active), function ($query) use ($active) {
                $query->where('active', '=', $active);
            })
            ->get()
            ->toArray();

        return response()
            ->json($client);
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
    public function store(StoreClientRequest $request)
    {
        $client = $this->client->create(
            InsertClientDto::make($request)->toArray()
        );

        return response()
            ->json([
                'errors' => [
                    'description' => 'nada para voce aqui',
                ],
                'data' => $client
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, int $id)
    {
        $client = $this->client->findOrFail($id);
        
        $client->update($request->all());
        
        return response()
            ->json($client);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $client = $this->client->findOrFail($id);
        
        $client->delete();

        return response()
            ->noContent();
    }

    public static function clientConfigConnection(Client $client): void
    {
        $client = $client->query()
            ->where('id', 1)
            ->first();

        empty($client)
            ? throw new EmptyClientsException('Não há cliente cadastrado para os parametros. Verique!')
            : null;

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

        return;
    }

    public static function clientConnection(Client $client): Connection
    {
        DB::purge('origem');
        DB::reconnect('origem');
        
        return DB::connection(
           'origem'
        );
    }
}
