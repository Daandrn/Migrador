<?php 

namespace App\Checks;

use App\Contracts\CommandCheckInterface;
use App\Repository\VerifyErrorRepository;
use App\Service\CheckService;
use App\Service\ClientService;
use Illuminate\Database\Connection;
use Exception;

class CommandCheck implements CommandCheckInterface
{
    /**
     * @var string[] $sqlChecks
     */
    protected array $sqlChecks = [];

    public function __construct(
        protected VerifyErrorRepository $verifyErrorRepository,
        protected ClientService $clientService,
        protected CheckService $checkService,
    ) {
        //
    }
    
    public function add(string $sqlCheck)
    {
        $this->sqlChecks[] = $sqlCheck;
        
        return;
    }

    public function execute(int $clientId)
    {
        $clientConn = $this->clientService->clientConnection(id: $clientId);
        
        foreach ($this->sqlChecks as $check) {
            $check = trim($check);
            
            $this->checkService->verifyMultSql(sql: $check);

           if (!$this->executeInClient(clientConn: $clientConn, sql_query: $check)) {
                throw new Exception('Não foi possível gravar o registro.');
           }
        };
    }

    protected function executeInClient(Connection $clientConn, string $sql_query): true
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

        return true;
    }
}
