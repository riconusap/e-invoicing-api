<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::first();

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'role_id' => $role->id,
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'role_id' => $role->id,
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create additional users
        User::create([
            'name' => 'Manager User',
            'role_id' => $role->id,
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Finance User',
            'role_id' => $role->id,
            'email' => 'finance@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
