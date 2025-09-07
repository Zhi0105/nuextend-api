<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Program::create([ 'department_id' => 1, 'name' => 'BS Business Administration' ]);
        Program::create([ 'department_id' => 1, 'name' => 'BS Accountancy' ]);
        Program::create([ 'department_id' => 1, 'name' => 'BS Management Accounting' ]);


        Program::create([ 'department_id' => 2, 'name' => 'BS Psychology' ]);
        Program::create([ 'department_id' => 2, 'name' => 'Bachelor of Physical Education' ]);
        Program::create([ 'department_id' => 2, 'name' => 'Bachelor of Arts in English Language Studies' ]);
        Program::create([ 'department_id' => 2, 'name' => 'Bachelor of Arts in Economics' ]);

        Program::create([ 'department_id' => 3, 'name' => 'BS Civil Engineering' ]);
        Program::create([ 'department_id' => 3, 'name' => 'BS Computer Engineering' ]);
        Program::create([ 'department_id' => 3, 'name' => 'BS Information Technology' ]);

        Program::create([ 'department_id' => 4, 'name' => 'BS Architecture' ]);

        Program::create([ 'department_id' => 5, 'name' => 'BS Hospitality Management' ]);
        Program::create([ 'department_id' => 5, 'name' => 'BS Tourism Management' ]);

        Program::create([ 'department_id' => 6, 'name' => 'Accountancy Business and Management' ]);
        Program::create([ 'department_id' => 6, 'name' => 'Science Technology Engineering and Mathematics' ]);
        Program::create([ 'department_id' => 6, 'name' => 'Humanities and Social Sciences' ]);


    }
}
