<?php 

namespace App\Checks;

use App\Contracts\CommandCheckInterface;
use App\Http\Controllers\Api\ClientController;
use App\Models\Client;
use App\Repository\ErrorRepository;
use Illuminate\Database\Connection;
use Exception;

class CommandCheck implements CommandCheckInterface
{
    /**
     * @var string[] $sqlChecks
     */
    protected array $sqlChecks = [];
    protected Connection $clientConnection;

    public function __construct(
        protected ErrorRepository $errorRepository,
        protected Client $client,
    ) {
        //
    }
    
    public function add(string $sqlCheck)
    {
        $this->sqlChecks[] = $sqlCheck;
        
        return;
    }

    public function execute()
    {
        ClientController::clientConfigConnection($this->client);
        $this->clientConnection = ClientController::clientConnection($this->client);
        
        foreach ($this->sqlChecks as $check) {
            $sql = trim($check);
            
            if (substr_count($sql, ';') > 1) {
                throw new Exception('Múltiplos comandos não são permitidos.');
            }

           if (!$this->executeInClient($check)) {
                throw new Exception('Não foi possível gravar o registro.');
           }
        };
    }

    protected function executeInClient(string $sql_query): true
    {
        $data = $this->clientConnection->select(trim($sql_query));

        if (empty($data)) {
            throw new Exception('Não foram localizados registros para serem lançados.');
        }

        
        foreach ($data as $item) {
            $saved = $this->errorRepository->create($item);

            if (!$saved) {
                throw new Exception('Não foi possível gravar o registro: ' . json_encode($item));
            }
        }

        return true;
    }

    /**
     * @param string[] $types 
     */
    public function getErrors(?array $types = null): array
    { 
        return $this->errorRepository->get($types);
    }
}
