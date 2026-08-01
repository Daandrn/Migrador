<?php 

namespace App\Http\Controllers\Api;

use App\DTO\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\VerifyType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyTypeController extends Controller
{
    public function __construct(
        protected VerifyType $verifyType,
    ) {
        //
    }
    
    public function get(Request $request): JsonResponse
    {
        $verifyTypes = $this->verifyType
            ->where('active', true)
            ->orderBy('description')
            ->get();

        $response = ApiResponse::make(
                success: true,
                message: 'Busca de tipos de erros de verificação realizada com sucesso!',
                data: [
                    'verifyTypes' => $verifyTypes->toArray()
                ],
                statusCode: 200
            );
        
        return response()
            ->json(
                data: $response->toArray(), 
                status: $response->statusCode,
            );  
    }
}
