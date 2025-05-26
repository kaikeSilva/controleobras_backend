<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cliente;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FontePagadora>
 */
class FontePagadoraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clientIds = Cliente::pluck('id')->toArray();
        return [
            'nome' => $this->faker->company(),
            'descricao' => $this->faker->sentence(10),
            'ativo' => $this->faker->boolean(90),
            'data_cadastro' => now(),
            'cliente_id' => $this->faker->randomElement($clientIds),
            'status' => $this->faker->randomElement(['ativo', 'inativo']),
        ];
    }
}
