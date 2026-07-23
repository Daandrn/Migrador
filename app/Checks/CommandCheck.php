<?php 

namespace App\Checks;

use App\Contracts\CommandCheckInterface;
use App\Http\Controllers\Api\ClientesController;
use App\Models\Clientes;
use App\Repository\ErrorRepository;
use Illuminate\Database\Connection;
use Exception;

class CommandCheck implements CommandCheckInterface
{
    /**
     * @var string[] $sqlChecks
     */
    protected array $sqlChecks = [];
    protected Connection $clienteConnection;

    public function __construct(
        protected ErrorRepository $errorRepository,
        protected Clientes $clientes,
    ) {
        //
    }
    
    public function adicionar(string $sqlCheck)
    {
        $this->sqlChecks[] = $sqlCheck;
        
        return;
    }

    public function executar()
    {
        ClientesController::clienteConfigConnection($this->clientes);
        $this->clienteConnection = ClientesController::clienteConnection($this->clientes);
        
        foreach ($this->sqlChecks as $check) {
            $sql = trim($check);
            
            if (substr_count($sql, ';') > 1) {
                throw new Exception('Múltiplos comandos não são permitidos.');
            }

           if (!$this->executarNoCliente($check)) {
                throw new Exception('Não foi possível gravar o registro.');
           }
        };
    }

    protected function executarNoCliente(string $consulta_sql): true
    {
        $data = $this->clienteConnection->select(trim($consulta_sql));

        if (empty($data)) {
            throw new Exception('Não foram localizados registros para serem lançados.');
        }

        
        foreach ($data as $item) {
            $gravou = $this->errorRepository->gravar($item);

            if (!$gravou) {
                throw new Exception('Não foi possível gravar o registro: ' . json_encode($item));
            }
        }

        return true;
    }

    /**
     * @param string[] $tipos 
     */
    public function buscarErros(?array $tipos = null): array
    { 
        return $this->errorRepository->buscar($tipos);
    }
}
