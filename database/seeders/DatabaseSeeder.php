<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);


        // Executa os seeders necessários
        $this->call([
            \Database\Seeders\ClienteSeeder::class,
            \Database\Seeders\ObraSeeder::class,
            \Database\Seeders\CategoriaGastoSeeder::class,
            \Database\Seeders\FontePagadoraSeeder::class,
            \Database\Seeders\GastoSeeder::class,
            \Database\Seeders\EntradaRecursoSeeder::class,
        ]);
    }
}
