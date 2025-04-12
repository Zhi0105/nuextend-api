<?php

namespace Database\Seeders;

use App\Models\Unsdg;
use Illuminate\Database\Seeder;

class UnsdgSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unsdg::create([ 'name' => 'No Poverty' ]);
        Unsdg::create([ 'name' => 'Zero Hunger' ]);
        Unsdg::create([ 'name' => 'Good Health and Well-being' ]);
        Unsdg::create([ 'name' => 'Quality Education' ]);
        Unsdg::create([ 'name' => 'Gender Equality' ]);
        Unsdg::create([ 'name' => 'Clean Water and Sanitation' ]);
        Unsdg::create([ 'name' => 'Affordable and Clean Energy' ]);
        Unsdg::create([ 'name' => 'Decent Work and Economic Growth' ]);
        Unsdg::create([ 'name' => 'Industry, Innovation and Infrastructure' ]);
        Unsdg::create([ 'name' => 'Reduced Inequalities' ]);
        Unsdg::create([ 'name' => 'Sustainable Cities and Communities' ]);
        Unsdg::create([ 'name' => 'Responsible Consumption and Production' ]);
        Unsdg::create([ 'name' => 'Climate Action' ]);
        Unsdg::create([ 'name' => 'Life Below Water' ]);
        Unsdg::create([ 'name' => 'Life on Land' ]);
        Unsdg::create([ 'name' => 'Peace, Justice and Strong Institutions' ]);
        Unsdg::create([ 'name' => 'Partnerships for the Goals' ]);
    }
}
