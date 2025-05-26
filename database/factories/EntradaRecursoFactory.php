<?php

namespace Database\Factories;

use App\Models\EntradaRecurso;
use App\Models\Obra;
use App\Models\FontePagadora;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntradaRecursoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EntradaRecurso::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $obra = Obra::inRandomOrder()->first() ?? Obra::factory()->create();
        $fontePagadora = FontePagadora::inRandomOrder()->first() ?? FontePagadora::factory()->create();
        $tipos = array_keys(EntradaRecurso::TIPOS_ENTRADA);
        
        return [
            'obra_id' => $obra->id,
            'fonte_pagadora_id' => $fontePagadora->id,
            'valor' => $this->faker->randomFloat(2, 100, 100000),
            'data_entrada' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'descricao' => $this->faker->sentence(6),
            'comprovante_url' => $this->faker->optional(0.7)->url,
            'tipo_entrada' => $this->faker->randomElement($tipos),
        ];
    }
}
