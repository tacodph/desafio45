<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        DB::table('user_types')->insert([
            ['name' => 'administrador'],
            ['name' => 'nutricionista'],
            ['name' => 'coach'],
            ['name' => 'marketing'],
            ['name' => 'atleta'],
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_type_id')->nullable()->constrained('user_types');
        });

        $atletaId = DB::table('user_types')->where('name', 'atleta')->value('id');

        DB::table('users')->update([
            'user_type_id' => DB::raw('(select id from user_types where user_types.name = users.role)'),
        ]);

        DB::table('users')->whereNull('user_type_id')->update(['user_type_id' => $atletaId]);

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('user_type_id')->nullable(false)->change();
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('atleta');
        });

        DB::table('users')->update([
            'role' => DB::raw('(select name from user_types where user_types.id = users.user_type_id)'),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_type_id');
        });

        Schema::dropIfExists('user_types');
    }
};
