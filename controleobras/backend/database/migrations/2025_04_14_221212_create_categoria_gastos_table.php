<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriaGastosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categoria_gastos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->timestamps();
        });

        // Criar tabela pivot para relacionamento muitos para muitos com obras
        Schema::create('categoria_gasto_obra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_gasto_id')->constrained()->onDelete('cascade');
            $table->foreignId('obra_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Garantir que cada combinação seja única
            $table->unique(['categoria_gasto_id', 'obra_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categoria_gasto_obra');
        Schema::dropIfExists('categoria_gastos');
    }
}
