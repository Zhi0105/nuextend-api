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
        // Helper function to read image data safely
        $getEsign = function (string $filename) {
            $path = storage_path('app/public/attachments/' . $filename);
            return file_exists($path) ? file_get_contents($path) : null;
        };

        User::create([
            'role_id' => 1,
            'firstname' => 'fioglo',
            'middlename' => 'c',
            'lastname' => 'baluyot',
            'email' => 'admin@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('admin123'),
            'contact' => '09111111111',
            'esign' => $getEsign('comex_esign.png'),
        ]);

        User::create([
            'role_id' => 10,
            'firstname' => 'bon kervin',
            'middlename' => 'd',
            'lastname' => 'venturina',
            'email' => 'asd@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('asd123'),
            'contact' => '09999999999',
            'esign' => $getEsign('asd_esign.png'),
        ]);

        User::create([
            'role_id' => 11,
            'firstname' => 'jayson raymond',
            'middlename' => 'd',
            'lastname' => 'bermudez',
            'email' => 'ad@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('ad123'),
            'contact' => '09999999999',
            'esign' => $getEsign('ad_esign.png'),
        ]);

        User::create([
            'role_id' => 9,
            'department_id' => 1,
            'firstname' => 'fransis allan',
            'middlename' => 'C',
            'lastname' => 'bernales',
            'email' => 'deansba@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999',
            'esign' => $getEsign('deansba_esign.png'),
        ]);

        User::create([
            'role_id' => 9,
            'department_id' => 2,
            'firstname' => 'ma. liwayway',
            'middlename' => 'f',
            'lastname' => 'fernando',
            'email' => 'deanseas@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999',
            'esign' => $getEsign('deanseas_esign.png'),
        ]);

        User::create([
            'role_id' => 9,
            'department_id' => 3,
            'firstname' => 'jenalyn',
            'middlename' => 'd',
            'lastname' => 'columna',
            'email' => 'deanseat@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999',
            'esign' => $getEsign('deanseat_esign.png'),
        ]);

        User::create([
            'role_id' => 9,
            'department_id' => 4,
            'firstname' => 'chona',
            'middlename' => 'e',
            'lastname' => 'ponce',
            'email' => 'deansa@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999',
            'esign' => $getEsign('deansa_esign.png'),
        ]);

        User::create([
            'role_id' => 9,
            'department_id' => 5,
            'firstname' => 'mark chris',
            'middlename' => 'm',
            'lastname' => 'lapuz',
            'email' => 'deansthm@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999',
            'esign' => $getEsign('deansthm_esign.png'),
        ]);

        User::create([
            'role_id' => 9,
            'department_id' => 6,
            'firstname' => 'shs',
            'middlename' => 'nu',
            'lastname' => 'dean',
            'email' => 'deanshs@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('dean123'),
            'contact' => '09999999999',
            'esign' => $getEsign('deanshs_esign.png'),
        ]);

        User::create([
            'role_id' => 12,
            'firstname' => 'rufino',
            'middlename' => 'c',
            'lastname' => 'oliva',
            'email' => 'ed@nu-baliwag.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('ed123'),
            'contact' => '09999999999',
            'esign' => $getEsign('ed_esign.png'),
        ]);
    }
}
