<?php

use App\Http\Controllers\Api\{
    CheckController,
    ClientController,
    VerifyErrorController,
    VerifyTypeController,
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::delete('cliente/{id}', [ClientController::class, 'destroy'])->name('api.clients.destroy');
Route::put('cliente/{id}', [ClientController::class, 'update'])->name('api.clients.update');
Route::get('cliente/{id}/verificar-usuario', [ClientController::class, 'userVerify'])->name('api.clients.UserVerify');
Route::post('cliente', [ClientController::class, 'store'])->name('api.clients.insert');
Route::get('cliente', [ClientController::class, 'get'])->name('api.clients');


Route::delete('checagem/{id}', [CheckController::class, 'destroy'])->name('api.checks.destroy');
Route::put('checagem/{id}', [CheckController::class, 'update'])->name('api.checks.update');
Route::post('checagem', [CheckController::class, 'store'])->name('api.checks.insert');
Route::get('checagem', [CheckController::class, 'get'])->name('api.checks');
Route::post('checagem/executar', [CheckController::class, 'execute'])->name('api.checks.execute');

Route::delete('verificacao', [VerifyErrorController::class, 'destroy'])->name('api.verifyErrors.destroy');

Route::get('verificacao/erros', [VerifyErrorController::class, 'get'])->name('api.verifyErrors');
Route::get('verificacao/tipos', [VerifyTypeController::class, 'get'])->name('api.verifyTypes');

Route::middleware('auth:sanctum')->group(function () {
    //
});
