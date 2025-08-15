<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default user if none exists
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]
        );

        // Create sample employees
        Employee::factory(20)->create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }
}
