<?php 

namespace App\Http\Controllers\Api;

use App\Checks\CommandCheck;
use App\DTO\InsertCheckDto;
use App\DTO\UpdateCheckDto;
use App\Helpers\NormalizeSql;
use App\Http\Requests\Api\Checks\StoreCheckRequest;
use App\Http\Requests\Api\Checks\UpdateCheckRequest;
use App\Models\{
    Check,
};
use App\Repository\CheckRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CheckController
{
    public function __construct(
        protected CommandCheck $commandCheck,
        protected CheckRepository $checkRepository,
        protected Check $check,
    ) {
        //
    }

    public function index(): JsonResponse
    {
        $checks = $this->checkRepository->buscar(null);

        return response()
            ->json($checks);
    }

    public function store(StoreCheckRequest $request): bool
    {
        return $this->checkRepository->insert(InsertCheckDto::make($request));
    }

    public function update(UpdateCheckRequest $request): bool
    {
        $request->merge([
            'consulta_sql' => NormalizeSql::make($request->consulta_sql),
            'descricao' => trim($request->input('descricao')),
            'tipo' => trim($request->input('tipo')),
        ]);
        
        return $this->checkRepository->update(UpdateCheckDto::make($request));
    }

    public function destroy(int $id)
    {
        return $this->checkRepository->delete($id);
    }

    public function init(): JsonResponse
    {        
        try {
            $this->check
                ->All()
                ->each(function ($check) {
                    $this->commandCheck->adicionar($check->consulta_sql);
                });

            DB::beginTransaction();

            $this->commandCheck->executar();

            DB::commit();

            return Response()
            ->json([
                'message' => 'Verificações concluídas!',
                'error' => false,
                'data' => json_encode($this->commandCheck->buscarErros())
            ]);
        } catch (\Throwable $error) {
            DB::rollBack();
            
            return Response()
                ->json([
                    'message' => 'Falha ao realizar verificações: ' . json_encode($error->getMessage()),
                    'error' => true,
                    'data' => null
                ]);
        }
    }
}
