<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Cliente;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoriaGasto>
 */
class CategoriaGastoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prefixos = [
            'Gastos com ',
            'Despesas de ',
            'Custos de ',
            'Investimento em ',
            'Recursos para ',
            'Materiais de ',
            'Serviços de ',
        ];

        $categorias = [
            'mão de obra',
            'material de construção',
            'alimentação',
            'transporte',
            'equipamentos',
            'serviços terceirizados',
            'materiais elétricos',
            'hidráulica',
            'acabamento',
            'projetos e taxas',
            'segurança do trabalho',
            'limpeza',
            'pintura',
            'fundação',
            'estrutura',
            'cobertura',
            'esquadrias',
            'revestimentos',
            'elétrica',
            'gás',
            'impermeabilização'
        ];

        $prefixo = $this->faker->optional(0.7)->randomElement($prefixos);
        $categoria = $this->faker->randomElement($categorias);
        
        // Gera um nome base
        $nomeBase = ($prefixo ? $prefixo : '') . $categoria;
        
        // Gera um ID único para garantir que o slug seja único
        $uniqueId = $this->faker->unique()->numberBetween(1, 10000);
        
        // Adiciona o ID único ao nome e gera o slug
        $nome = $nomeBase . ' ' . $uniqueId;
        $slug = \Illuminate\Support\Str::slug($nome);
        
        return [
            'nome' => $nome,
            'slug' => $slug,
            'status' => $this->faker->randomElement(['ativo', 'inativo']), // Valores de enum válidos
            'cliente_id' => $this->faker->optional(0.7)->randomElement(Cliente::pluck('id')->toArray()),
            'descricao' => $this->faker->optional(0.8)->sentence(),
            'cor' => $this->faker->optional(0.7)->hexColor(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
