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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CheckController extends Controller
{
    public function __construct(
        protected CommandCheck $commandCheck,
        protected CheckRepository $checkRepository,
        protected Check $check,
    ) {
        //
    }

    public function getAll(): JsonResponse
    {
        $checks = $this->checkRepository->getChecks(null);

        return response()
            ->json($checks);
    }

    public function store(StoreCheckRequest $request)
    {
        $this->checkRepository->create(
            InsertCheckDto::make($request)
        );

        return response()
            ->json([
                'errors' => [
                    'description' => 'nada para voce aqui',
                ],
            ]);
    }

    public function update(UpdateCheckRequest $request): bool
    {
        $request->merge([
            'sql_query' => NormalizeSql::make($request->sql_query),
            'description' => trim($request->input('description')),
            'type_id' => trim($request->input('type_id')),
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
                    $this->commandCheck->add($check->sql_query);
                });

            DB::beginTransaction();

            $this->commandCheck->execute();

            DB::commit();

            return Response()
            ->json([
                'message' => 'Verificações concluídas!',
                'error' => false,
                'data' => json_encode($this->commandCheck->getErrors())
            ]);
        } catch (\Throwable $error) {
            DB::rollBack();
            
            return Response()
                ->json([
                    'message' => 'Falha ao realizar verificações: ' . json_encode($error->getMessage()),
                    'error' => true,
                    'data' => []
                ]);
        }
    }
}
