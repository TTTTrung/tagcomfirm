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
        $role = Role::create(['name' => 'scanner']);
        $role = Role::create(['name' => 'commoner']);
        $role = Role::create(['name' => 'plAdmin']);
        $role = Role::create(['name' => 'superAdmin']);
    }
}
