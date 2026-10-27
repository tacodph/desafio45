<?php

use App\Support\ShoppingCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_groups', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('name')->default('');
            $table->json('notes')->nullable();
            $table->unsignedSmallInteger('position');
            $table->timestamps();
        });

        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('position');
            $table->timestamps();
        });

        $path = base_path('docs/documentos/lista_compras_inteligente_nutri_gente.md');

        if (is_file($path)) {
            ShoppingCatalog::import((string) file_get_contents($path));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('foods');
        Schema::dropIfExists('food_groups');
    }
};
