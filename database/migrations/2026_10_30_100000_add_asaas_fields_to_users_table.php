<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('asaas_customer_id')->nullable()->unique();
            $table->timestamp('challenge_paid_at')->nullable();
        });

        // Quem já existe antes do pagamento entrar no ar (testes, equipe, atletas atuais) não é bloqueado.
        DB::table('users')->update(['challenge_paid_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['asaas_customer_id']);
            $table->dropColumn(['asaas_customer_id', 'challenge_paid_at']);
        });
    }
};
