<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkins', function (Blueprint $table) {
            $table->string('meal_photo')->nullable()->after('workout_photo');
            $table->string('meal_comment')->nullable()->after('meal_photo');
        });
    }

    public function down(): void
    {
        Schema::table('checkins', function (Blueprint $table) {
            $table->dropColumn(['meal_photo', 'meal_comment']);
        });
    }
};
