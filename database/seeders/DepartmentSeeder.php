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
        Department::create([ 'name' => 'Senior High School' ]);
        Department::create([ 'name' => 'Engineering and Technology' ]);
        Department::create([ 'name' => 'Education, Arts and Sciences' ]);
        Department::create([ 'name' => 'Computing and Information Technologies' ]);
        Department::create([ 'name' => 'Business and Accountancy programs' ]);
    }
}
