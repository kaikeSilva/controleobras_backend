<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('obras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('endereco')->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('prazo_estimado')->nullable();
            $table->decimal('valor_estimado', 15, 2)->nullable();
            $table->decimal('taxa_administracao', 5, 2)->nullable();
            $table->string('status')->default('ativo');
            $table->boolean('ativo')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obras');
    }
};
