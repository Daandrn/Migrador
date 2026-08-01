<?php 

namespace App\Http\Controllers\Api;

use App\Checks\CommandCheck;
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

        return response()
            ->json(data: $checks);
    }

    public function store(StoreCheckRequest $request)
    {
        $request->merge([
            'sql_query' => NormalizeSQl::make(sql: $request->input('sql_query')),
            'description' => trim($request->input('description')),
        ]);

        $this->checkService->verifyMultSql(sql: $request->input('sql_query'));
        
        $this->checkRepository->create(
            data: InsertCheckDto::make(request: $request)
        );

        $message = 'Checagem incluída com sucesso!';
        $error   = false;
        $data['errors'] = [
                'description' => 'nada para voce aqui',
            ];

        return Response()
            ->json([
                'message' => $message,
                'error'   => $error,
                'data'    => $data,
            ]);
    }

    public function update(UpdateCheckRequest $request): bool
    {
        $request->merge([
            'sql_query' => NormalizeSql::make(sql: $request->input('sql_query')),
            'description' => trim($request->input('description')),
        ]);

        $this->checkService->verifyMultSql(sql: $request->input('sql_query'));
        
        return $this->checkRepository->update(data: UpdateCheckDto::make($request));
    }

    public function destroy(int $id)
    {
        try {
            DB::beginTransaction();
            
            $this->checkRepository->delete(id: $id);

            DB::commit();

            $message = 'Checagem excluída com sucesso!';
            $error   = false;
            $data    = [];
        } catch (\Throwable $error) {
            DB::rollBack();
            
            $message = 'Erro ao excluir checagem: ' . $error->getMessage();
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

            $message = 'Verificações concluídas!';
            $error   = false;
            $data    = [];
        } catch (\Throwable $error) {
            DB::rollBack();
            
            $message = 'Falha ao realizar verificações: ' . json_encode($error->getMessage());
            $error   = true;
            $data    = [];
        }

        return Response()
            ->json([
                'message' => $message,
                'error'   => $error,
                'data'     => $data,
            ]);
    }
}
