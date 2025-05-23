<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([ 'name' => 'admin' ]);
        Role::create([ 'name' => 'guest' ]);
        Role::create([ 'name' => 'student' ]);
        Role::create([ 'name' => 'faculty' ]);
        Role::create([ 'name' => 'staff' ]);
        Role::create([ 'name' => 'leader' ]);
        Role::create([ 'name' => 'organizer' ]);
        Role::create([ 'name' => 'member' ]);
        Role::create([ 'name' => 'dean' ]);
        Role::create([ 'name' => 'ASD' ]);
        Role::create([ 'name' => 'AD' ]);
    }
}
