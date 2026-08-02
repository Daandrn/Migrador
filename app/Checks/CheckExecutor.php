<?php 

namespace App\Checks;

use App\Contracts\CheckExecutorInterface;
use App\Models\Client;
use App\Repository\VerifyErrorRepository;
use App\Service\CheckService;
use App\Service\ClientService;
use App\Types\SqlQuery;
use Illuminate\Database\Connection;
use Exception;

class CheckExecutor implements CheckExecutorInterface
{
    /**
     * @var SqlQuery[] $queryChecks
     */
    protected array $queryChecks;

    public function __construct(
        protected VerifyErrorRepository $verifyErrorRepository,
        protected ClientService $clientService,
        protected CheckService $checkService,
    ) {
        //
    }
    
    protected function add(SqlQuery $queryCheck)
    {
        $this->queryChecks[] = $queryCheck;
        
        return;
    }

    public function run(Client $client, array $checks)
    {
        if (empty($checks)) {
            throw new Exception("Não há checagens para serem executadas, verifique!");
        }
        
        foreach ($checks as $check) {
            $this->add(
                queryCheck: new SqlQuery($check['sql_query'])
            );
        }
        
        $clientConn = $this->clientService->validAndConnect(client: $client);

        foreach ($this->queryChecks as $check) {
           if (!$this->executeInClient(clientConn: $clientConn, sql_query: new SqlQuery($check))) {
                throw new Exception('Não foi possível gravar o registro.');
           }
        };

        unset($this->queryChecks);

        return;
    }

    protected function executeInClient(Connection $clientConn, SqlQuery $sql_query): true
    {
        $data = $clientConn->select(query: $sql_query);

        if (empty($data)) {
            throw new Exception('Não foram localizados registros para serem lançados.');
        }

        
        foreach ($data as $item) {
            $saved = $this->verifyErrorRepository->create(data: $item);

            if (!$saved) {
                throw new Exception('Não foi possível gravar o registro: ' . json_encode($item));
            }
        }

        unset($data);

        return true;
    }
}
