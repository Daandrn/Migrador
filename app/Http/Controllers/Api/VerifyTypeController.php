<?php 

namespace App\Http\Controllers\Api;

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

        return response()
            ->json(data: $verifyTypes->toArray());
    }
}
