<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => "kaike",
            'email' => "k@email.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);


        // Executa os seeders necessários
        $this->call([
            // \Database\Seeders\ClienteSeeder::class,
            // \Database\Seeders\ObraSeeder::class,
            // \Database\Seeders\CategoriaGastoSeeder::class,
            // \Database\Seeders\FontePagadoraSeeder::class,
            // \Database\Seeders\GastoSeeder::class,
            // \Database\Seeders\EntradaRecursoSeeder::class,
            // \Database\Seeders\ExemploSeeder::class,
        ]);
    }
}
