<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FontePagadora;

class FontePagadoraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FontePagadora::factory(20)->create();
    }
}
