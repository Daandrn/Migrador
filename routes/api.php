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

Route::delete('clients/{id}', [ClientController::class, 'destroy'])->name('api.clients.destroy');
Route::put('clients/{id}', [ClientController::class, 'update'])->name('api.clients.update');
Route::get('clients/{id}', [ClientController::class, 'userVerify'])->name('api.clients.UserVerify');
Route::post('clients/create', [ClientController::class, 'store'])->name('api.clients.insert');
Route::get('clients', [ClientController::class, 'get'])->name('api.clients');


Route::delete('checks/{id}', [CheckController::class, 'destroy'])->name('api.checks.destroy');
Route::put('checks/{id}', [CheckController::class, 'update'])->name('api.checks.update');
Route::post('checks/create', [CheckController::class, 'store'])->name('api.checks.insert');
Route::get('checks', [CheckController::class, 'get'])->name('api.checks');
Route::post('checks/executar', [CheckController::class, 'execute'])->name('api.checks.execute');

Route::delete('verificacao/erros', [VerifyErrorController::class, 'destroy'])->name('api.verifyErrors.destroy');
Route::get('verificacao/erros', [VerifyErrorController::class, 'get'])->name('api.verifyErrors');

Route::get('verificacao/tipos', [VerifyTypeController::class, 'get'])->name('api.verifyTypes');

Route::middleware('auth:sanctum')->group(function () {
    //
});
