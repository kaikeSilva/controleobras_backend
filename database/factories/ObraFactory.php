<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cliente;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Obra>
 */
class ObraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Garante que exista pelo menos um cliente
        $client = Cliente::inRandomOrder()->first() ?? Cliente::factory()->create();
        
        return [
            'cliente_id' => $client->id,
            'nome' => $this->faker->company(),
            'descricao' => $this->faker->sentence(12),
            'endereco' => $this->faker->address(),
            'area_m2' => $this->faker->randomFloat(2, 50, 10000),
            'data_inicio' => $this->faker->date(),
            'prazo_estimado' => $this->faker->date(),
            'valor_estimado' => $this->faker->randomFloat(2, 10000, 10000000),
            'taxa_administracao' => $this->faker->randomFloat(2, 5, 20),
            'status' => $this->faker->randomElement(['em_andamento', 'concluida', 'pausada']),
            'ativo' => $this->faker->boolean(90),
        ];
    }
}
