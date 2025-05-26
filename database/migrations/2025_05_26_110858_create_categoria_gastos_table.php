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
        Schema::create('categoria_gastos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->boolean('status')->default(true);
            $table->foreignId('cliente_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->text('descricao')->nullable();
            $table->string('cor', 7)->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Índices
            $table->index('nome');
            $table->index('slug');
            $table->index('cliente_id');
            
            // Restrições de unicidade
            $table->unique(['nome', 'cliente_id']);
            $table->unique(['slug', 'cliente_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categoria_gastos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
        });
        Schema::dropIfExists('categoria_gastos');
    }
};
