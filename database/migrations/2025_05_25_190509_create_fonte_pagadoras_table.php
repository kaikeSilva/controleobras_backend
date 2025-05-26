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
        Schema::create('fonte_pagadoras', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamp('data_cadastro')->useCurrent();
            // cliente_id pode ser nulo
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->timestamps();
            $table->softDeletes(); // deleted_at
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fonte_pagadoras');
    }
};
