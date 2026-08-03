<?php 

namespace App\Services;

use App\DTO\Check\CheckDto;
use App\DTO\Check\InsertCheckDto;
use App\DTO\Check\UpdateCheckDto;
use App\Models\Check;
use App\Repositories\CheckRepository;
use App\Types\SqlQuery;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckService
{
    public function __construct(
        protected CheckRepository $checkRepository,
    ) {
        //
    }

    public function get(?array $types): array
    {
        return $this->checkRepository->getChecks($types);
    }

    /**
     * @param int[] $ids
     * @return Check[]
     */
    public function getById(array $ids): array
    {
        if (empty($ids)) {
            throw new Exception("Nenhum id informado para busca de checagens, verifique!");
        }

        $checks = $this->checkRepository->getById($ids);

        if (empty($checks)) {
            throw new Exception("Nenhuma checagem encontrada, verifique!");
        }

        return $checks;
    }

    public function store(InsertCheckDto $dto): CheckDto
    {
        $validatedDto = new InsertCheckDto(
            description: $dto->description,
            type_id: $dto->type_id,
            sql_query: $dto->sql_query,
            active: $dto->active,
        );

        return DB::transaction(
            function () use ($validatedDto): CheckDto
            {
                return $this->checkRepository->create($validatedDto);
            }
        );
    }

    public function update(UpdateCheckDto $dto): bool
    {
        $validatedDto = new UpdateCheckDto(
            id: $dto->id,
            description: $dto->description,
            type_id: $dto->type_id,
            sql_query: new SqlQuery($dto->sql_query),
            active: $dto->active,
        );

        return DB::transaction(
            function () use ($validatedDto): bool
            {
                return $this->checkRepository->update($validatedDto);
            }
        );
    }

    public function delete(int $id): bool
    {
        return DB::transaction(
            function () use ($id): bool
            {
                return $this->checkRepository->delete($id);
            }
        );
    }
}
