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
            'email_verified_at' => now(),
            'password' => bcrypt('admin123'),
            'contact' => '21387129831983'
        ]);

        User::create([
            'role_id' => 10,
            'firstname' => 'asd',
            'middlename' => 'asd',
            'lastname' => 'asd',
            'email' => 'asd@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('asd123'),
            'contact' => '09999999999'
        ]);

        User::create([
            'role_id' => 11,
            'firstname' => 'ad',
            'middlename' => 'ad',
            'lastname' => 'ad',
            'email' => 'ad@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('ad123'),
            'contact' => '09999999999'
        ]);



        User::create([
            'role_id' => 9,
            'firstname' => 'sba',
            'middlename' => 'nu',
            'lastname' => 'dean',
            'email' => 'deansba@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999'
        ]);

        User::create([
            'role_id' => 9,
            'firstname' => 'seas',
            'middlename' => 'nu',
            'lastname' => 'dean',
            'email' => 'deanseas@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999'
        ]);

        User::create([
            'role_id' => 9,
            'firstname' => 'set',
            'middlename' => 'nu',
            'lastname' => 'dean',
            'email' => 'deanset@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999'
        ]);

        User::create([
            'role_id' => 9,
            'firstname' => 'sa',
            'middlename' => 'nu',
            'lastname' => 'dean',
            'email' => 'deansa@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999'
        ]);

        User::create([
            'role_id' => 9,
            'firstname' => 'sthm',
            'middlename' => 'nu',
            'lastname' => 'dean',
            'email' => 'deansthm@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999'
        ]);

        User::create([
            'role_id' => 9,
            'firstname' => 'shs',
            'middlename' => 'nu',
            'lastname' => 'dean',
            'email' => 'deanshs@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999'
        ]);

    }
}
