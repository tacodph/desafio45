<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('athlete_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('movement_level')->nullable();
            $table->unsignedSmallInteger('movement_minutes')->nullable();
            $table->decimal('water_liters', 4, 1)->nullable();
            $table->string('sleep_goal')->nullable();
            $table->string('skill')->nullable();
            $table->string('strength_goal')->nullable();
            $table->string('test_1')->nullable();
            $table->string('test_15')->nullable();
            $table->string('test_30')->nullable();
            $table->string('test_45')->nullable();
            $table->boolean('penalty')->default(false);
            $table->date('penalty_donated_on')->nullable();
            $table->string('penalty_note')->nullable();
            $table->timestamps();
        });

        Schema::create('checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day');
            $table->boolean('checked_in')->default(false);
            $table->boolean('movement')->default(false);
            $table->boolean('water')->default(false);
            $table->boolean('sleep')->default(false);
            $table->boolean('strength')->default(false);
            $table->boolean('skill')->default(false);
            $table->unsignedTinyInteger('plus_count')->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'day']);
        });

        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('achieved_on');
            $table->string('type');
            $table->string('detail')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('checkins');
        Schema::dropIfExists('athlete_profiles');
    }
};
