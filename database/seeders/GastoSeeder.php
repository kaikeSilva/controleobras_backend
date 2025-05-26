<?php

namespace Database\Seeders;

use App\Models\Gasto;
use Illuminate\Database\Seeder;

class GastoSeeder extends Seeder
{
    public function run()
    {
        Gasto::factory(50)->create();
    }
}
