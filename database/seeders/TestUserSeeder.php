<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        // Create student role if it doesn't exist
        $studentRole = Role::firstOrCreate(
            ['name' => 'student'],
            ['description' => 'Student role with limited access']
        );

        // Create test user
        User::create([
            'name' => 'Arrey Johnson',
            'email' => 'arrey.johnson@baotechnologiesandtravels.com',
            'password' => Hash::make('@BAOTECH237'),
            'email_verified_at' => now(),
            'role_id' => $studentRole->id
        ]);
    }
}
