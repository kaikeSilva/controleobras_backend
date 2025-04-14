<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('obras', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('endereco');
            $table->date('data_inicio');
            $table->integer('prazo_estimado'); // em dias
            $table->decimal('area_m2', 10, 2);
            $table->decimal('valor_estimado', 15, 2);
            $table->decimal('taxa_administracao', 5, 2); // em porcentagem
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('obras');
    }
}
