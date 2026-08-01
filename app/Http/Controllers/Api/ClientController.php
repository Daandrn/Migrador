<?php

namespace App\Http\Controllers\Api;

use App\DTO\Client\InsertClientDto;
use App\DTO\Client\UpdateClientDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Client\StoreClientRequest;
use App\Http\Requests\Api\Client\UpdateClientRequest;
use App\Service\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $service,
        protected ClientService $clientService,
    ) {
        //
    }   
    
    /**
     * Display a listing of the resource.
     */
    public function get(Request $request): JsonResponse
    {
        $active = (bool) true;
        
        $clients = $this->service->get(onlyActives: $active);

        return response()
            ->json(data: $clients);
    }

    public function userVerify(int $id): JsonResponse
    {
        try {
            $this->clientService->clientConnection(id: $id);
            
            $message = 'Usuário somente leitura, pode ser usado com segurança!';
            $error   = false;
            $data    = [];
        } catch (\Throwable $error) {
            $message = 'Erro durante a verificação do usuário: ' . $error->getMessage();
            $error   = true;
            $data    = [];
        }

        return Response()
            ->json(data: [
                'message' => $message,
                'error'   => $error,
                'data'    => $data
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        $client = $this->service->store(
            dto: InsertClientDto::make(request: $request)
        );

        return response()
            ->json(data: [
                'errors' => [
                    'description' => 'nada para voce aqui',
                ],
                'data' => $client
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, int $id)
    {
        $client = $this->service->update(
            dto: UpdateClientDto::make(request: $request),
            id: $id
        );
        
        return response()
            ->json(data: $client);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            DB::beginTransaction();
            
            $this->service->delete(id: $id);

            DB::commit();

            $message = 'Cliente excluído com sucesso!';
            $error   = false;
            $data    = [];
        } catch (\Throwable $error) {
            DB::rollBack();
            
            $message = 'Erro ao excluir cliente: ' . $error->getMessage();
            $error   = true;
            $data    = [];
        }

        return Response()
            ->json([
                'message' => $message,
                'error'   => $error,
                'data'    => $data
            ]);
    }
}
