<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('athlete_profiles', function (Blueprint $table) {
            $table->timestamp('goals_set_at')->nullable()->after('welcomed_at');
        });

        // Quem já preencheu alguma meta antes desta etapa não precisa preencher de novo.
        DB::table('athlete_profiles')
            ->whereNotNull('welcomed_at')
            ->where(function ($query) {
                $query->whereNotNull('movement_level')
                    ->orWhereNotNull('movement_minutes')
                    ->orWhereNotNull('water_liters')
                    ->orWhereNotNull('sleep_goal')
                    ->orWhereNotNull('skill')
                    ->orWhereNotNull('strength_goal');
            })
            ->update(['goals_set_at' => DB::raw('welcomed_at')]);
    }

    public function down(): void
    {
        Schema::table('athlete_profiles', function (Blueprint $table) {
            $table->dropColumn('goals_set_at');
        });
    }
};
