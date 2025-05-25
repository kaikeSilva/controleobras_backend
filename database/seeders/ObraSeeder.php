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
        $statusList = ['ativo', 'inativo'];
        Obra::factory(20)->make()->each(function($obra) use ($statusList) {
            $obra->status = $statusList[array_rand($statusList)];
            $obra->save();
        });
    }
}
