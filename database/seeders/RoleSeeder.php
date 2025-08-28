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
        Role::create([ 'name' => 'Admin' ]);
        Role::create([ 'name' => 'Guest' ]);
        Role::create([ 'name' => 'Students' ]);
        Role::create([ 'name' => 'Faculty' ]);
        Role::create([ 'name' => 'Staff' ]);
        Role::create([ 'name' => 'Leader' ]);
        Role::create([ 'name' => 'Organizer' ]);
        Role::create([ 'name' => 'Member' ]);
        Role::create([ 'name' => 'Dean' ]);
        Role::create([ 'name' => 'ASD' ]);
        Role::create([ 'name' => 'AD' ]);
    }
}
