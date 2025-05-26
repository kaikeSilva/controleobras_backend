<?php

namespace Database\Seeders;

use App\Models\EntradaRecurso;
use App\Models\Obra;
use App\Models\FontePagadora;
use Illuminate\Database\Seeder;

class EntradaRecursoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que existam obras e fontes pagadoras
        if (Obra::count() === 0) {
            Obra::factory(5)->create();
        }
        
        if (FontePagadora::count() === 0) {
            FontePagadora::factory(3)->create();
        }

        // Cria 50 entradas de recursos
        EntradaRecurso::factory(50)->create();
    }
}
