<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->foreignId('categoria_gasto_id')->constrained('categoria_gastos')->onDelete('restrict');
            $table->foreignId('fonte_pagadora_id')->constrained('fonte_pagadoras')->onDelete('restrict');
            $table->text('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('data_compra');
            $table->date('data_pagamento')->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('comprovante_url')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gastos');
    }
};
