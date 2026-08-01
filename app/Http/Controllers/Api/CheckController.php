<?php 

namespace App\Http\Controllers\Api;

use App\Checks\CommandCheck;
use App\DTO\ApiResponse;
use App\DTO\Check\InsertCheckDto;
use App\DTO\Check\UpdateCheckDto;
use App\Helpers\NormalizeSql;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Check\StoreCheckRequest;
use App\Http\Requests\Api\Check\UpdateCheckRequest;
use App\Models\{
    Check,
};
use App\Repository\CheckRepository;
use App\Repository\VerifyErrorRepository;
use App\Service\CheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckController extends Controller
{
    public function __construct(
        protected CommandCheck $commandCheck,
        protected CheckRepository $checkRepository,
        protected VerifyErrorRepository $verifyErrorRepository,
        protected Check $check,
        protected CheckService $checkService,
    ) {
        //
    }

    public function get(Request $request): JsonResponse
    {
        $checks = $this->checkRepository->getChecks(types: null);

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

        $this->checkService->verifyMultSql(sql: $request->input('sql_query'));
        
        $createdCheck = $this->checkRepository->create(
            data: InsertCheckDto::make(request: $request)
        );

        $response = ApiResponse::make(
            success: true,
            message: 'Checagem incluída com sucesso!',
            data: [
                'check' => $createdCheck
            ]
        );

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
            $this->checkService->verifyMultSql(sql: $request->input('sql_query'));

            DB::beginTransaction();

            $hasUpdated = $this->checkRepository->update(data: UpdateCheckDto::make($request));

            DB::commit();
            
            $response = ApiResponse::make(
                success: $hasUpdated,
                message: 'Checagem atualizada com sucesso!',
            );
        } catch (\Throwable $error) {
            DB::rollBack();

            $response = ApiResponse::make(
                success: false,
                message: 'Erro ao atualizar checagem: ' . $error->getMessage(),
                statusCode: 422
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
            DB::beginTransaction();
            
            $this->checkRepository->delete(id: $id);

            DB::commit();

            $response = ApiResponse::make(
                success: true,
                message: 'Checagem excluída com sucesso!',
            );
        } catch (\Throwable $error) {
            DB::rollBack();

            $response = ApiResponse::make(
                success: false,
                message: 'Erro ao excluir checagem: ' . $error->getMessage(),
                statusCode: 422
            );
        }

        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );
    }

    public function init(): JsonResponse
    {        
        try {
            $this->check
                ->All()
                ->each(function ($check) {
                    $this->commandCheck->add(sqlCheck: $check->sql_query);
                });

            DB::beginTransaction();

            $client_id = 5;

            $this->commandCheck->execute(clientId: $client_id);

            DB::commit();

            $response = ApiResponse::make(
                success: true,
                message: 'Verificações concluídas!',
            );
        } catch (\Throwable $error) {
            DB::rollBack();

            $response = ApiResponse::make(
                success: false,
                message: 'Falha ao realizar verificações: ' . $error->getMessage(),
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
