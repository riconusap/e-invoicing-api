<?php

namespace Database\Seeders;

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
        $adminUser = User::where('email', 'admin@example.com')->first();

        $employees = [
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'full_name' => 'John Doe',
                'nik' => '1234567890123456',
                'nip' => '123456789012345678',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'full_name' => 'Jane Smith',
                'nik' => '2345678901234567',
                'nip' => '234567890123456789',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'full_name' => 'Bob Johnson',
                'nik' => '3456789012345678',
                'nip' => '345678901234567890',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'full_name' => 'Alice Brown',
                'nik' => '4567890123456789',
                'nip' => '456789012345678901',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
        ];

        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }
    }
}
