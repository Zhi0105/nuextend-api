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
        Department::create([ 'name' => 'School of Business and Accountancy' ]);
        Department::create([ 'name' => 'School of Education, Arts and Sciences' ]);
        Department::create([ 'name' => 'School of Engineering and Technology' ]);
        Department::create([ 'name' => 'School of Architecture' ]);
        Department::create([ 'name' => 'School of Tourism and Hospitality Management' ]);
        Department::create([ 'name' => 'Senior High School' ]);
    }
}
