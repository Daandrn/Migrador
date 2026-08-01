<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repository\VerifyErrorRepository;
use Exception;
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
        
        return Response()
            ->json(data: $VerifyErrors);
    }

    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $this->repository->delete(ids: $request->input('ids'));

            DB::commit();

            $message = 'Erro de checagem excluído com sucesso!';
            $error   = false;
            $data    = [];
        } catch (\Throwable $error) {
            DB::rollBack();
            
            $message = 'Erro ao excluir Erro de checagem: ' . $error->getMessage();
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
