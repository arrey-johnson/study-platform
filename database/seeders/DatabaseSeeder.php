<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator role with full access']
        );

        $studentRole = Role::firstOrCreate(
            ['name' => 'student'],
            ['description' => 'Student role with limited access']
        );

        // Create admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'contact@baotechnologiesandtravels.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('@BAOTECH237'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Create student user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'arrey.johnson@baotechnologiesandtravels.com'],
            [
                'name' => 'Arrey Johnson',
                'password' => Hash::make('@BAOTECH237'),
                'role_id' => $studentRole->id,
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            StudentSeeder::class,
            CategorySeeder::class,
        ]);
    }
}
