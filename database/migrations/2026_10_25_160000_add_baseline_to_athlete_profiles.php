<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('athlete_profiles', function (Blueprint $table) {
            $table->decimal('weight', 5, 1)->nullable();
            $table->decimal('waist', 5, 1)->nullable();
            $table->decimal('abdomen', 5, 1)->nullable();
            $table->decimal('hip', 5, 1)->nullable();
            $table->string('photo')->nullable();
            $table->timestamp('welcomed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('athlete_profiles', function (Blueprint $table) {
            $table->dropColumn(['weight', 'waist', 'abdomen', 'hip', 'photo', 'welcomed_at']);
        });
    }
};
