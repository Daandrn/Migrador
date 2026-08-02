<?php 

namespace App\Http\Controllers\Api;

use App\Checks\CheckExecutor;
use App\DTO\ApiResponse;
use App\DTO\ApiResponseError;
use App\DTO\Check\{
    InsertCheckDto,
    UpdateCheckDto,
};
use App\Helpers\NormalizeSql;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Check\{
    ExecuteChecksRequest,
    StoreCheckRequest,
    UpdateCheckRequest,
};
use App\Models\{
    Check,
};
use App\Repositories\{
    VerifyErrorRepository,
};
use App\Services\CheckService;
use App\Services\ClientService;
use Illuminate\Http\{
    JsonResponse,
    Request,
};
use Illuminate\Support\Facades\DB;

class CheckController extends Controller
{
    public function __construct(
        protected CheckExecutor $checkExecutor,
        protected VerifyErrorRepository $verifyErrorRepository,
        protected Check $check,
        protected CheckService $checkService,
        protected ClientService $clientService,
    ) {
        //
    }

    public function get(Request $request): JsonResponse
    {
        $checks = $this->checkService->get(types: null);

        $response = ApiResponse::make(
            success: true,
            message: 'Busca de checagens realizada com sucesso!',
            data: [
                'checks' => $checks
            ]
        );

        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );
    }

    public function store(StoreCheckRequest $request): JsonResponse
    {
        $request->merge([
            'sql_query' => NormalizeSQl::make(sql: $request->input('sql_query')),
            'description' => trim($request->input('description')),
        ]);
        
        try {
            $createdCheck = $this->checkService->store(
                dto: InsertCheckDto::make(request: $request)
            );

            $response = ApiResponse::make(
                success: true,
                message: 'Checagem salva com sucesso!',
                data: [
                    'check' => $createdCheck
                ]
            );
        } catch (\Throwable $error) {
            $response = ApiResponse::make(
                success: false,
                message: 'Erro ao criar checagem: ' . $error->getMessage(),
                statusCode: 422,
            )
            ->setErrors(ApiResponseError::make(
                'ERRO',
                'Erro ao criar checagem: ' . $error->getMessage()
            ));
        }

        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );
    }

    public function update(UpdateCheckRequest $request): JsonResponse
    {
        $request->merge([
            'sql_query' => NormalizeSql::make(sql: $request->input('sql_query')),
            'description' => trim($request->input('description')),
        ]);

        try {
            $hasUpdated = $this->checkService->update(dto: UpdateCheckDto::make($request));
            
            $response = ApiResponse::make(
                success: $hasUpdated,
                message: 'Checagem atualizada com sucesso!',
            );
        } catch (\Throwable $error) {
            $response = ApiResponse::make(
                success: false,
                message: 'Erro ao atualizar checagem: ' . $error->getMessage(),
                statusCode: 422,
            );
        }

        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->checkService->delete(id: $id);

            $response = ApiResponse::make(
                success: true,
                message: 'Checagem excluída com sucesso!',
            );
        } catch (\Throwable $error) {
            $response = ApiResponse::make(
                success: false,
                message: 'Erro ao excluir checagem: ' . $error->getMessage(),
                statusCode: 422,
            );
        }

        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );
    }

    public function execute(ExecuteChecksRequest $request): JsonResponse
    {
        try {
            $client = $this->clientService->find(
                id: $request->integer('client_id'),
            );

            $checks = $this->checkService->getById(
                ids: $request->input('check_ids'),
            );

            $this->checkExecutor->run(
                client: $client,
                checks: $checks,
            );

            $response = ApiResponse::make(
                success: true,
                message: 'Verificações concluídas!',
            );
        } catch (\Throwable $error) {
            DB::rollBack();

            $response = ApiResponse::make(
                success: false,
                message: 'Falha ao realizar verificações: ' . $error->getMessage(),
                statusCode: 422,
            );
        }

        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );
    }
}
