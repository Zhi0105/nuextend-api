<?php

namespace Database\Seeders;

use App\Models\EventStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventStatus::create([ 'name' => 'active' ]);
        EventStatus::create([ 'name' => 'completed' ]);
        EventStatus::create([ 'name' => 'pending' ]);
        EventStatus::create([ 'name' => 'submitted' ]);
        EventStatus::create([ 'name' => 'pulled-back' ]);
        EventStatus::create([ 'name' => 'revise' ]);
        EventStatus::create([ 'name' => 'approve' ]);
        EventStatus::create([ 'name' => 'revised' ]);
        EventStatus::create([ 'name' => 'resubmitted' ]);
        // WALA PANG NAIISIP NA ILALAGAY
    }
}
