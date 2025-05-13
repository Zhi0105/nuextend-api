<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'role_id' => 1,
            'firstname' => 'admin',
            'middlename' => 'admin',
            'lastname' => 'admin',
            'email' => 'admin@nu-baliwag.edu.ph',
            'password' => bcrypt('admin123'),
            'contact' => '21387129831983'
        ]);

        User::create([
            'role_id' => 10,
            'firstname' => 'asd',
            'middlename' => 'asd',
            'lastname' => 'asd',
            'email' => 'asd@nu-baliwag.edu.ph',
            'password' => bcrypt('asd123'),
            'contact' => '09999999999'
        ]);

        User::create([
            'role_id' => 11,
            'firstname' => 'ad',
            'middlename' => 'ad',
            'lastname' => 'ad',
            'email' => 'ad@nu-baliwag.edu.ph',
            'password' => bcrypt('ad123'),
            'contact' => '09999999999'
        ]);

    }
}
