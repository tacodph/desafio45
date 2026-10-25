<?php

use App\Http\Controllers\AthleteProgressController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('participar', function () {
    return Inertia::render('Participar', [
        'paymentUrl' => config('desafio.payment_url'),
    ]);
})->name('participar');

Route::redirect('objetivos', '/objetivos/alimentacao')->name('objetivos');
Route::redirect('objetivos/oracao', '/objetivos/plus');
Route::redirect('objetivos/corpo', '/objetivos/alimentacao');
Route::redirect('objetivos/flexoes', '/objetivos/alimentacao');

Route::get('objetivos/alimentacao/compras', [AthleteProgressController::class, 'shopping'])->name('alimentacao.compras');

Route::get('objetivos/{goal}', function (string $goal) {
    $keys = ['alimentacao', 'treino', 'hidratacao', 'habilidade', 'plus'];

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
    Route::get('equipe/atletas/{athlete}/evolucao', [AthleteProgressController::class, 'showAthlete'])->name('equipe.athlete.progress');
    Route::get('atleta/boas-vindas', [AthleteProgressController::class, 'welcome'])->name('athlete.welcome');
    Route::post('atleta/boas-vindas', [AthleteProgressController::class, 'storeWelcome'])->name('athlete.welcome.store');
    Route::get('atleta/evolucao', [AthleteProgressController::class, 'show'])->name('athlete.progress');
    Route::put('atleta/metas', [AthleteProgressController::class, 'updateGoals'])->name('athlete.goals.update');
    Route::put('atleta/habilidades', [AthleteProgressController::class, 'updateSkills'])->name('athlete.skills.update');
    Route::put('objetivos/alimentacao/compras', [AthleteProgressController::class, 'updateShopping'])->name('alimentacao.compras.update');
    Route::put('atleta/dias', [AthleteProgressController::class, 'updateDays'])->name('athlete.days.update');
    Route::post('atleta/conquistas', [AthleteProgressController::class, 'storeAchievement'])->name('athlete.achievements.store');
    Route::put('atleta/conquistas/{achievement}', [AthleteProgressController::class, 'updateAchievement'])->name('athlete.achievements.update');
    Route::put('atleta/conquistas/{achievement}/comentario', [AthleteProgressController::class, 'commentAchievement'])->name('athlete.achievements.comment');
    Route::delete('atleta/conquistas/{achievement}', [AthleteProgressController::class, 'destroyAchievement'])->name('athlete.achievements.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
