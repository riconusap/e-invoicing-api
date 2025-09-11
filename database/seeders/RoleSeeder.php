<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin role
        Role::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'admin',
        ]);
    }
}
