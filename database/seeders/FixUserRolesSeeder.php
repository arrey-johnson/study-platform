<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FixUserRolesSeeder extends Seeder
{
    public function run()
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator role with full access']
        );

        $studentRole = Role::firstOrCreate(
            ['name' => 'student'],
            ['description' => 'Student role with limited access']
        );

        // Set admin account
        User::where('email', 'contact@baotechnologiesandtravels.com')
            ->update(['role_id' => $adminRole->id]);

        // Set student account
        User::where('email', 'arrey.johnson@baotechnologiesandtravels.com')
            ->update(['role_id' => $studentRole->id]);
    }
}
