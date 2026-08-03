<?php 

namespace App\Checks;

use App\Contracts\CheckExecutorInterface;
use App\Models\Check;
use App\Models\Client;
use App\Repositories\VerifyErrorRepository;
use App\Services\CheckService;
use App\Services\ClientService;
use App\Types\SqlQuery;
use Illuminate\Database\Connection;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckExecutor implements CheckExecutorInterface
{
    public function __construct(
        protected VerifyErrorRepository $verifyErrorRepository,
        protected ClientService $clientService,
        protected CheckService $checkService,
    ) {
        //
    }

    /**
     * @var Check[] $checks
     */
    public function run(Client $client, array $checks): void
    {
        if (empty($checks)) {
            throw new Exception("Não há checagens para serem executadas, verifique!");
        }
        
        $clientConn = $this->clientService->validAndConnect(client: $client);

        foreach ($checks as $check) {
            $data = $this->executeInClient(clientConn: $clientConn, sql_query: new SqlQuery($check['sql_query']));
            
            if (empty($data)) {
                continue;
            }

            $typeId = $check['type_id'];

            DB::transaction(
                function () use ($data, $typeId) 
                {
                    foreach ($data as $item) {
                        $saved = $this->verifyErrorRepository->create(
                            data: $item,
                            type: $typeId,
                        );

                        if (!$saved) {
                            throw new Exception('Não foi possível gravar o registro: ' . json_encode($item, JSON_THROW_ON_ERROR));
                        }
                    }
                }
            );

            unset($data);
        };

        unset($checks);

        return;
    }

    protected function executeInClient(Connection $clientConn, SqlQuery $sql_query): array
    {
        return $clientConn->select(query: $sql_query);
    }
}
