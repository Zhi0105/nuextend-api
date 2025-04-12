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

        Program::create([ 'department_id' => 1, 'name' => 'Sciences, Technology, Engineering and Mathematics' ]);
        Program::create([ 'department_id' => 1, 'name' => 'Humanities and Social Sciences' ]);
        Program::create([ 'department_id' => 1, 'name' => 'Accountancy, Business, and Mathematics' ]);


        Program::create([ 'department_id' => 2, 'name' => 'BS Architecture' ]);
        Program::create([ 'department_id' => 2, 'name' => 'BS Computer Engineering' ]);
        Program::create([ 'department_id' => 2, 'name' => 'BS Civil Engineering' ]);

        Program::create([ 'department_id' => 3, 'name' => 'BA Economics' ]);
        Program::create([ 'department_id' => 3, 'name' => 'BA English Language Studies' ]);
        Program::create([ 'department_id' => 3, 'name' => 'BS Psychology' ]);
        Program::create([ 'department_id' => 3, 'name' => 'Bachelor in Physical Education' ]);
        Program::create([ 'department_id' => 3, 'name' => 'MA in Education Major in Special Education' ]);
        Program::create([ 'department_id' => 3, 'name' => 'Doctor of Education Major in Education Management' ]);
        Program::create([ 'department_id' => 3, 'name' => 'MA in Education Major in Educational Management' ]);
        Program::create([ 'department_id' => 3, 'name' => 'MA in Education Major in English
' ]);
        Program::create([ 'department_id' => 4, 'name' => 'BS Information Technology with specialization in Mobile and Web Applications' ]);

        Program::create([ 'department_id' => 5, 'name' => 'BS Tourism Management' ]);
        Program::create([ 'department_id' => 5, 'name' => 'Master in Management' ]);
        Program::create([ 'department_id' => 5, 'name' => 'BS Accounting Information System' ]);
        Program::create([ 'department_id' => 5, 'name' => 'BS Business Administration Major in Financial Management' ]);
        Program::create([ 'department_id' => 5, 'name' => 'BS Business Administration major in Marketing' ]);
        Program::create([ 'department_id' => 5, 'name' => 'BS Accountancy' ]);
        Program::create([ 'department_id' => 5, 'name' => 'BS Hospitality Management' ]);

    }
}
