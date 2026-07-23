<?php

use App\Http\Controllers\Api\{
    CheckController,
    ClientesController,
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('clientes', [ClientesController::class, 'index'])->name('clientes.index');
Route::put('clientes/{id}', [ClientesController::class, 'update'])->name('clientes.update');
Route::delete('clientes/{id}', [ClientesController::class, 'destroy'])->name('clientes.destroy');

Route::get('testes', [CheckController::class, 'init'])->name('checks.init');
Route::get('checks', [CheckController::class, 'index'])->name('checks.index');
Route::put('checks/{id}', [CheckController::class, 'update'])->name('checks.update');
Route::delete('checks/{id}', [CheckController::class, 'destroy'])->name('checks.destroy');

Route::middleware('auth:sanctum')->group(function () {
    //
});
