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
        Moddel::create([ 'name' => 'Outreach Project' ]);
        Moddel::create([ 'name' => 'Project' ]);
        Moddel::create([ 'name' => 'Program' ]);
        Moddel::create([ 'name' => 'Emergency' ]);
    }
}
