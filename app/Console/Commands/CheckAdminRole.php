<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;

class CheckAdminRole extends Command
{
    protected $signature = 'admin:check';
    protected $description = 'Check and fix admin role';

    public function handle()
    {
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            $this->error('Admin role not found!');
            return;
        }

        $admin = User::where('email', 'contact@baotechnologiesandtravels.com')->first();
        if (!$admin) {
            $this->error('Admin user not found!');
            return;
        }

        $this->info("Admin Role ID: " . $adminRole->id);
        $this->info("Admin User Role ID: " . $admin->role_id);
        $this->info("Is Admin?: " . ($admin->isAdmin() ? 'Yes' : 'No'));
    }
}
