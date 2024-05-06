<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    protected static ?string $password;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'superadmin',
            'email' => 'admin@thairung.co.th',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('passadmin'), 
        ]);
        $user->assignRole('superAdmin');
    }
}
