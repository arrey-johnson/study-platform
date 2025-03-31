<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure roles table exists and has required roles
        if (Schema::hasTable('roles')) {
            // Check if admin role exists
            $adminExists = DB::table('roles')->where('name', 'admin')->exists();
            if (!$adminExists) {
                DB::table('roles')->insert([
                    'name' => 'admin',
                    'description' => 'Administrator role with full access',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Check if student role exists
            $studentExists = DB::table('roles')->where('name', 'student')->exists();
            if (!$studentExists) {
                DB::table('roles')->insert([
                    'name' => 'student',
                    'description' => 'Student role with limited access',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Get role IDs
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $studentRoleId = DB::table('roles')->where('name', 'student')->value('id');
        
        // Make sure users have correct role_id
        if (Schema::hasTable('users') && $adminRoleId && $studentRoleId) {
            // Create admin user if not exists
            $adminExists = DB::table('users')->where('email', 'admin@example.com')->exists();
            if (!$adminExists) {
                DB::table('users')->insert([
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRoleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'email_verified_at' => now(),
                ]);
            } else {
                // Update existing admin user
                DB::table('users')
                    ->where('email', 'admin@example.com')
                    ->update(['role_id' => $adminRoleId]);
            }
            
            // Create student user if not exists
            $studentExists = DB::table('users')->where('email', 'student@example.com')->exists();
            if (!$studentExists) {
                DB::table('users')->insert([
                    'name' => 'Student User',
                    'email' => 'student@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $studentRoleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'email_verified_at' => now(),
                ]);
            } else {
                // Update existing student user
                DB::table('users')
                    ->where('email', 'student@example.com')
                    ->update(['role_id' => $studentRoleId]);
            }
            
            // Make sure the BAO emails have correct roles
            if (DB::table('users')->where('email', 'contact@baotechnologiesandtravels.com')->exists()) {
                DB::table('users')
                    ->where('email', 'contact@baotechnologiesandtravels.com')
                    ->update(['role_id' => $adminRoleId]);
            }
            
            if (DB::table('users')->where('email', 'arrey.johnson@baotechnologiesandtravels.com')->exists()) {
                DB::table('users')
                    ->where('email', 'arrey.johnson@baotechnologiesandtravels.com')
                    ->update(['role_id' => $studentRoleId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only ensures data consistency, no need for a down method
    }
};
