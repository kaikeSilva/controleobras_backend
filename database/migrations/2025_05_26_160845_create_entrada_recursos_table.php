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
        Schema::create('entrada_recursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->foreignId('fonte_pagadora_id')->constrained('fonte_pagadoras')->onDelete('cascade');
            $table->decimal('valor', 15, 2);
            $table->date('data_entrada');
            $table->text('descricao');
            $table->string('comprovante_url')->nullable();
            $table->enum('tipo_entrada', ['aporte_inicial', 'aporte_adicional', 'reembolso', 'regular'])->default('regular');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrada_recursos', function (Blueprint $table) {
            $table->dropForeign(['obra_id']);
            $table->dropForeign(['fonte_pagadora_id']);
        });
        Schema::dropIfExists('entrada_recursos');
    }
};
