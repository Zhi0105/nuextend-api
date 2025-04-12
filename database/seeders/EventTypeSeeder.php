<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventType::create([ 'name' => 'Income Generating(inGen)' ]);
        EventType::create([ 'name' => 'Infastructure Development(infra)' ]);
        EventType::create([ 'name' => 'Community Wellness(comwell)' ]);
        EventType::create([ 'name' => 'educare' ]);
        EventType::create([ 'name' => 'Community Partnership(compartnership)' ]);
        EventType::create([ 'name' => 'Disaster Response, Rehabilitation(DRRR)' ]);
        EventType::create([ 'name' => 'Information and Advocacy(infoadvo)' ]);
        EventType::create([ 'name' => 'Kilos para sa Kapaligiran at kalikasan (Kali-kapa)' ]);
        EventType::create([ 'name' => 'Citezenship training' ]);
        EventType::create([ 'name' => 'Service Learning' ]);
    }
}
