<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = ['can_view_transactions', 'can_view_refunds', 'can_log_refunds', 'can_view_users', 'can_create_users', 'can_edit_users', 'can_blacklist', 'whitelist_users'];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}