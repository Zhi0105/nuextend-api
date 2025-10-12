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

        Program::create([ 'department_id' => 1, 'name' => 'BS Business Administration', 'initial' => 'BSBA' ]);
        Program::create([ 'department_id' => 1, 'name' => 'BS Accountancy', 'initial' => 'BSA' ]);
        Program::create([ 'department_id' => 1, 'name' => 'BS Management Accounting', 'initial' => 'BSMA' ]);


        Program::create([ 'department_id' => 2, 'name' => 'BS Psychology' , 'initial' => 'BSP' ]);
        Program::create([ 'department_id' => 2, 'name' => 'Bachelor of Physical Education', 'initial' => 'BPE' ]);
        Program::create([ 'department_id' => 2, 'name' => 'Bachelor of Arts in English Language Studies', 'initial' => 'BAELS' ]);
        Program::create([ 'department_id' => 2, 'name' => 'Bachelor of Arts in Economics', 'initial' => 'BAE' ]);

        Program::create([ 'department_id' => 3, 'name' => 'BS Civil Engineering', 'initial' => 'BSCE' ]);
        Program::create([ 'department_id' => 3, 'name' => 'BS Computer Engineering', 'initial' => 'BSCE' ]);
        Program::create([ 'department_id' => 3, 'name' => 'BS Information Technology', 'initial' => 'BSIT' ]);

        Program::create([ 'department_id' => 4, 'name' => 'BS Architecture', 'initial' => 'BSA' ]);

        Program::create([ 'department_id' => 5, 'name' => 'BS Hospitality Management', 'initial' => 'BSHM' ]);
        Program::create([ 'department_id' => 5, 'name' => 'BS Tourism Management', 'initial' => 'BSTM' ]);

        Program::create([ 'department_id' => 6, 'name' => 'Accountancy Business and Management', 'initial' => 'ABM' ]);
        Program::create([ 'department_id' => 6, 'name' => 'Science Technology Engineering and Mathematics', 'initial' => 'STEM' ]);
        Program::create([ 'department_id' => 6, 'name' => 'Humanities and Social Sciences', 'initial' => 'HSS' ]);


    }
}
