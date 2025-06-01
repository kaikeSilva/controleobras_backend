<?php

namespace Database\Seeders;

use App\Models\Exemplo;
use Illuminate\Database\Seeder;

class ExemploSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exemplo::factory(10)->create();
    }
}
