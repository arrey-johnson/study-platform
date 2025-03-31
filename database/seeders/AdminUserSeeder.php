<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator role with full access']
        );

        // Update your user to be an admin
        User::where('email', 'arrey.johnson@baotechnologiesandtravels.com')
            ->update(['role_id' => $adminRole->id]);
    }
}
