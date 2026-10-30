<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_rules', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group');
            $table->string('label');
            $table->unsignedTinyInteger('points');
            $table->unsignedTinyInteger('sort');
            $table->timestamps();
        });

        $now = now();

        DB::table('score_rules')->insert([
            ['key' => 'checkin', 'group' => 'daily', 'label' => 'Check-in enviado no dia', 'points' => 1, 'sort' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'movement', 'group' => 'daily', 'label' => 'Movimento e treino', 'points' => 3, 'sort' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'water', 'group' => 'daily', 'label' => 'Hidratação', 'points' => 1, 'sort' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'sleep', 'group' => 'daily', 'label' => 'Sono', 'points' => 1, 'sort' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'strength', 'group' => 'daily', 'label' => 'Alimentação e organização', 'points' => 2, 'sort' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'skill', 'group' => 'daily', 'label' => 'Habilidade nova', 'points' => 2, 'sort' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'consistency', 'group' => 'bonus', 'label' => 'Semana consistente, 6 de 7 dias (3 na semana 1)', 'points' => 10, 'sort' => 7, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'motivate', 'group' => 'bonus', 'label' => 'Motivar um amigo desanimado', 'points' => 5, 'sort' => 8, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'first_time', 'group' => 'bonus', 'label' => 'Primeira vez acertando algo novo', 'points' => 5, 'sort' => 9, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('score_rules');
    }
};
