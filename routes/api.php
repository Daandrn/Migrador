<?php

use App\Http\Controllers\Api\{
    CheckController,
    ClientController,
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
Route::post('clients/create', [ClientController::class, 'store'])->name('api.clients.insert');
Route::get('clients', [ClientController::class, 'getAll'])->name('api.clients');

Route::get('testes', [CheckController::class, 'init'])->name('api.checks.init');

Route::delete('checks/{id}', [CheckController::class, 'destroy'])->name('api.checks.destroy');
Route::put('checks/{id}', [CheckController::class, 'update'])->name('api.checks.update');
Route::post('checks/create', [CheckController::class, 'store'])->name('api.checks.insert');
Route::get('checks', [CheckController::class, 'getAll'])->name('api.checks');

Route::get('verify-types', [VerifyTypeController::class, 'getAll'])->name('api.verifyTypes');

Route::middleware('auth:sanctum')->group(function () {
    //
});
