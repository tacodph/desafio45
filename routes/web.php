<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::redirect('objetivos', '/objetivos/treino')->name('objetivos');

Route::get('objetivos/{goal}', function (string $goal) {
    $keys = ['treino', 'alimentacao', 'oracao', 'habilidade', 'flexoes'];

    if (! in_array($goal, $keys, true)) {
        abort(404);
    }

    return Inertia::render('Objetivos', [
        'goal' => $goal,
    ]);
})->name('objetivos.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('admin/usuarios', [DashboardController::class, 'users'])->name('admin.users');
    Route::get('admin/usuarios/novo', [DashboardController::class, 'createUser'])->name('admin.users.create');
    Route::post('admin/usuarios', [DashboardController::class, 'storeUser'])->name('admin.users.store');
    Route::get('equipe/atletas', [DashboardController::class, 'athletes'])->name('equipe.athletes');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
