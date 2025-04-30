<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\OrganizationMember;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $comex =  Organization::create([
            'name' => 'Comex'
        ]);

        OrganizationMember::create([
            'user_id' => 1,
            'organization_id' => $comex->id,
            'role_id' => 6
        ]);
    }
}
