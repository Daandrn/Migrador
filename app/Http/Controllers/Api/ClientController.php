<?php

namespace App\Http\Controllers\Api;

use App\DTO\ApiResponse;
use App\DTO\ApiResponseError;
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

        $response = ApiResponse::make(
            success: true,
            message: 'Busca de clientes realizada com sucesso!',
            data: [
                'clients' => $clients
            ],
            statusCode: 200
        );

        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            ); 
    }

    public function userVerify(int $id): JsonResponse
    {
        try {
            $this->clientService->clientConnection(id: $id);

            $response = ApiResponse::make(
                success: true,
                message: 'Usuário somente leitura, pode ser usado com segurança!',
                statusCode: 200
            );
        } catch (\Throwable $error) {
            $response = ApiResponse::make(
                success: false,
                message: 'Erro durante a verificação do usuário: ' . $error->getMessage(),
                statusCode: 422
            );
        }

        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = $this->service->store(
            dto: InsertClientDto::make(request: $request)
        );

        $response = ApiResponse::make(
            success: false,
            message: 'Cliente criado com sucesso!',
            data: [
                'client' => $client
            ],
            statusCode: 200
        )->setErrors(
            ApiResponseError::make(
                    code: 'ERRO_PADRONIZADO_AQUI',
                    message: 'nada para voce aqui',
        ));

        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );  
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        $client = $this->service->update(
            dto: UpdateClientDto::make(request: $request),
            id: $id
        );
        
        $response = ApiResponse::make(
            success: false,
            message: 'Cliente atualizado com sucesso!',
            data: [
                'client' => $client
            ],
            statusCode: 200
        );

        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $this->service->delete(id: $id);

            DB::commit();

            $response = ApiResponse::make(
                success: true,
                message: 'Cliente excluído com sucesso!',
                statusCode: 200
            );
        } catch (\Throwable $error) {
            DB::rollBack();

            $response = ApiResponse::make(
                success: false,
                message: 'Erro ao excluir cliente: ' . $error->getMessage(),
                statusCode: 422
            );
        }

        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );
    }
}
