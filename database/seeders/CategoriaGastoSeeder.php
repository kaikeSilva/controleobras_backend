<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\CategoriaGasto;
use App\Models\Cliente;

class CategoriaGastoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categorias padrão (sem cliente específico)
        $categoriasPadrao = [
            ['nome' => 'Mão de obra', 'descricao' => 'Gastos com mão de obra da equipe', 'cor' => '#3498db'],
            ['nome' => 'Material de construção', 'descricao' => 'Materiais básicos de construção', 'cor' => '#e74c3c'],
            ['nome' => 'Alimentação', 'descricao' => 'Refeições para a equipe', 'cor' => '#2ecc71'],
            ['nome' => 'Transporte', 'descricao' => 'Deslocamentos e fretes', 'cor' => '#f39c12'],
            ['nome' => 'Equipamentos', 'descricao' => 'Aluguel ou manutenção de equipamentos', 'cor' => '#9b59b6'],
            ['nome' => 'Serviços terceirizados', 'descricao' => 'Serviços contratados de terceiros', 'cor' => '#1abc9c'],
        ];

        foreach ($categoriasPadrao as $categoria) {
            CategoriaGasto::firstOrCreate(
                ['nome' => $categoria['nome'], 'cliente_id' => null],
                array_merge($categoria, [
                    'slug' => Str::slug($categoria['nome']),
                    'status' => 'ativo',
                ])
            );
        }

        // Criar categorias aleatórias usando o Factory         
        // Apenas se houver clientes no banco
        if (Cliente::exists()) {
            CategoriaGasto::factory(20)->create();
        }
    }
}
