<?php

namespace Database\Seeders;

use App\Models\Moddel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Moddel::create([ 'name' => 'out reach project' ]);
        Moddel::create([ 'name' => 'project' ]);
        Moddel::create([ 'name' => 'program' ]);
    }
}
