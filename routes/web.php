<?php

use App\Http\Controllers\Web\{
    ClientPageController,
    CheckPageController,
    VerifyErrorPageController,
};
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canResetPassword' => Route::has('register'),
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/clientes', [ClientPageController::class, 'index'])->name('clients');

    Route::get('/checagens', [CheckPageController::class, 'index'])->name('checks');

    Route::get('/erros', [VerifyErrorPageController::class, 'index'])->name('verifyErrors');

    
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

require __DIR__.'/auth.php';
