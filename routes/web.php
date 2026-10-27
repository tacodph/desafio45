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
    Route::get('meus-objetivos/{goal}', function (string $goal) {
        abort_unless(in_array($goal, ['alimentacao', 'treino', 'hidratacao', 'habilidade', 'plus'], true), 404);

        return Inertia::render('Objetivo', ['goal' => $goal]);
    })->name('meus-objetivos.show');
    Route::get('admin/usuarios', [DashboardController::class, 'users'])->name('admin.users');
    Route::get('admin/usuarios/novo', [DashboardController::class, 'createUser'])->name('admin.users.create');
    Route::post('admin/usuarios', [DashboardController::class, 'storeUser'])->name('admin.users.store');
    Route::get('admin/alimentos', [DashboardController::class, 'foods'])->name('admin.foods');
    Route::post('admin/alimentos', [DashboardController::class, 'storeFood'])->name('admin.foods.store');
    Route::patch('admin/alimentos/{food}', [DashboardController::class, 'toggleFood'])->name('admin.foods.toggle');
    Route::delete('admin/alimentos/{food}', [DashboardController::class, 'destroyFood'])->name('admin.foods.destroy');
    Route::get('equipe/atletas', [DashboardController::class, 'athletes'])->name('equipe.athletes');
    Route::get('equipe/atletas/{athlete}/evolucao', [AthleteProgressController::class, 'showAthlete'])->name('equipe.athlete.progress');
    Route::get('atleta/ponto-de-partida/primeiro-dia', [AthleteProgressController::class, 'firstDay'])->name('athlete.start.first-day');
    Route::post('atleta/ponto-de-partida/primeiro-dia', [AthleteProgressController::class, 'storeFirstDay'])->name('athlete.start.first-day.store');
    Route::get('atleta/ponto-de-partida/metas', [AthleteProgressController::class, 'goals'])->name('athlete.start.goals');
    Route::redirect('atleta/boas-vindas', '/atleta/ponto-de-partida/primeiro-dia');
    Route::redirect('atleta/evolucao/metas', '/atleta/ponto-de-partida/metas');
    Route::redirect('atleta/evolucao', '/atleta/evolucao/dias')->name('athlete.progress');
    Route::get('atleta/evolucao/{tab}', [AthleteProgressController::class, 'show'])
        ->whereIn('tab', ['dias', 'conquistas', 'resumo'])
        ->name('athlete.progress.tab');
    Route::put('atleta/metas', [AthleteProgressController::class, 'updateGoals'])->name('athlete.goals.update');
    Route::put('atleta/habilidades', [AthleteProgressController::class, 'updateSkills'])->name('athlete.skills.update');
    Route::get('objetivos/alimentacao/compras/nova', [AthleteProgressController::class, 'createShopping'])->name('alimentacao.compras.create');
    Route::get('objetivos/alimentacao/compras/{week}/editar', [AthleteProgressController::class, 'editShopping'])->whereNumber('week')->name('alimentacao.compras.edit');
    Route::post('objetivos/alimentacao/compras', [AthleteProgressController::class, 'storeShopping'])->name('alimentacao.compras.store');
    Route::put('objetivos/alimentacao/compras', [AthleteProgressController::class, 'updateShopping'])->name('alimentacao.compras.update');
    Route::put('atleta/dias', [AthleteProgressController::class, 'updateDays'])->name('athlete.days.update');
    Route::post('atleta/dias/{day}/foto-treino', [AthleteProgressController::class, 'storeWorkoutPhoto'])->whereNumber('day')->name('athlete.days.photo.store');
    Route::delete('atleta/dias/{day}/foto-treino', [AthleteProgressController::class, 'destroyWorkoutPhoto'])->whereNumber('day')->name('athlete.days.photo.destroy');
    Route::post('atleta/conquistas', [AthleteProgressController::class, 'storeAchievement'])->name('athlete.achievements.store');
    Route::put('atleta/conquistas/{achievement}', [AthleteProgressController::class, 'updateAchievement'])->name('athlete.achievements.update');
    Route::put('atleta/conquistas/{achievement}/comentario', [AthleteProgressController::class, 'commentAchievement'])->name('athlete.achievements.comment');
    Route::delete('atleta/conquistas/{achievement}', [AthleteProgressController::class, 'destroyAchievement'])->name('athlete.achievements.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
