<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obra;

class ObraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Obra::factory(20)->create();
    }
}
