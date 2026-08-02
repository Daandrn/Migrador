<?php

namespace App\Http\Controllers\Api;

use App\DTO\ApiResponse;
use App\Http\Controllers\Controller;
use App\Repositories\VerifyErrorRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifyErrorController extends Controller
{
    public function __construct(
        protected VerifyErrorRepository $repository,
    ) {
        //
    }

    public function get(Request $request): JsonResponse
    {
        $types = $request->input('types');
        
        $VerifyErrors = $this->repository->get(types: $types);

        $response = ApiResponse::make(
                success: true,
                message: 'Busca de erros de verificação realizada com sucesso!',
                data: [
                    'verifyErrors' => $VerifyErrors
                ],
                statusCode: 200
            );
        
        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );  
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $this->repository->delete(ids: $request->input('ids'));

            DB::commit();

            $response = ApiResponse::make(
                success: true,
                message: 'Erro de checagem excluído com sucesso!',
                statusCode: 200
            );
        } catch (\Throwable $error) {
            DB::rollBack();

            $response = ApiResponse::make(
                success: false,
                message: 'Erro ao excluir Erro de checagem: ' . $error->getMessage(),
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
