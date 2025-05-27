<?php

namespace Database\Factories;

use App\Models\Gasto;
use App\Models\Obra;
use App\Models\CategoriaGasto;
use App\Models\FontePagadora;
use Illuminate\Database\Eloquent\Factories\Factory;

class GastoFactory extends Factory
{
    protected $model = Gasto::class;

    public function definition()
    {
        $obra = Obra::all()->random();
        $categoriaGasto = CategoriaGasto::all()->random();
        $fontePagadora = FontePagadora::all()->random();

        return [
            'obra_id' => 4,
            'categoria_gasto_id' => $categoriaGasto->id,
            'fonte_pagadora_id' => $fontePagadora->id,
            'descricao' => $this->faker->sentence(4),
            'valor' => $this->faker->randomFloat(2, 10, 10000),
            'data_compra' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'data_pagamento' => $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
            'numero_documento' => $this->faker->optional(0.7)->numerify('NF-########'),
            'comprovante_url' => $this->faker->optional(0.6)->url,
            'observacoes' => $this->faker->optional(0.5)->paragraph,
        ];
    }
}
