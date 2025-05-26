<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        // Criar 10 clientes de exemplo
        for ($i = 0; $i < 10; $i++) {            
            Cliente::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'address' => $faker->address(),
                'status' => $faker->randomElement(['ativo', 'inativo']),
            ]);
        }
    }
}
