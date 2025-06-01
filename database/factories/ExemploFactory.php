<?php

namespace Database\Factories;

use App\Models\Exemplo;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExemploFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exemplo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->unique()->sentence(2),
            'status' => $this->faker->randomElement(['ativo', 'inativo']),
        ];
    }
}
