<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::create([ 'name' => 'School of Business and Accountancy', 'initial' => 'SBA' ]);
        Department::create([ 'name' => 'School of Education, Arts and Sciences', 'initial' => 'SEAS' ]);
        Department::create([ 'name' => 'School of Engineering and Technology', 'initial' => 'SET' ]);
        Department::create([ 'name' => 'School of Architecture', 'initial' => 'SA' ]);
        Department::create([ 'name' => 'School of Tourism and Hospitality Management', 'initial' => 'STHM' ]);
        Department::create([ 'name' => 'Senior High School', 'initial' => 'SHS' ]);
    }
}
