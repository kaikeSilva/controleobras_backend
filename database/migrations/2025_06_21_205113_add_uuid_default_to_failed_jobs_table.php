<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar a coluna uuid para ter um valor padrão gerado automaticamente
        Schema::table('failed_jobs', function (Blueprint $table) {
            // Primeiro, garantir que registros existentes tenham um UUID
            DB::table('failed_jobs')->whereNull('uuid')->orWhere('uuid', '')->update([
                'uuid' => DB::raw('REPLACE(UUID(), "-", "")')
            ]);
            
            // Depois, modificar a coluna para gerar UUIDs automaticamente
            $table->string('uuid')->default(Str::uuid()->toString())->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            // Remover o valor padrão
            $table->string('uuid')->default(null)->change();
        });
    }
};
