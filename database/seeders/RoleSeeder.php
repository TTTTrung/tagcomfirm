<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['scanner', 'commoner', 'plAdmin', 'plSuperAdmin', 'superAdmin','unlocker','lock'];

        foreach ($roles as $roleName) {
        // Check if a role with the current name already exists
        $existingRole = Role::where('name', $roleName)->first();

        // If the role doesn't exist, create it
        if (!$existingRole) {
            Role::create(['name' => $roleName]);
        }
    }
    }
}
