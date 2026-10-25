<?php

use App\Http\Controllers\AthleteProgressController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::redirect('objetivos', '/objetivos/treino')->name('objetivos');
Route::redirect('objetivos/alimentacao', '/objetivos/hidratacao');
Route::redirect('objetivos/oracao', '/objetivos/plus');
Route::redirect('objetivos/flexoes', '/objetivos/corpo');

Route::get('objetivos/{goal}', function (string $goal) {
    $keys = ['treino', 'hidratacao', 'habilidade', 'corpo', 'plus'];

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
    Route::get('atleta/evolucao', [AthleteProgressController::class, 'show'])->name('athlete.progress');
    Route::put('atleta/metas', [AthleteProgressController::class, 'updateGoals'])->name('athlete.goals.update');
    Route::put('atleta/dias', [AthleteProgressController::class, 'updateDays'])->name('athlete.days.update');
    Route::post('atleta/conquistas', [AthleteProgressController::class, 'storeAchievement'])->name('athlete.achievements.store');
    Route::delete('atleta/conquistas/{achievement}', [AthleteProgressController::class, 'destroyAchievement'])->name('athlete.achievements.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
