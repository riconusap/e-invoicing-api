<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractEmployee;
use App\Models\Employee;
use App\Models\Placement;
use App\Models\User;

class ContractEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        $employees = Employee::all();
        $placements = Placement::all();

        $contracts = [
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'nip' => '123456789012345678',
                'start_on' => '2024-01-01',
                'ends_on' => '2024-06-30',
                'thp' => 8000000,
                'daily_wages' => 400000,
                'account_number' => '1234567890',
                'bank_id' => 'BCA',
                'account_holder_name' => 'John Doe',
                'no_bpjstk' => 'BPJSTK001',
                'no_bpjskes' => 'BPJSKES001',
                'employee_id' => $employees->first()->id,
                'placement_id' => $placements->first()->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'nip' => '234567890123456789',
                'start_on' => '2024-02-01',
                'ends_on' => '2024-08-31',
                'thp' => 9000000,
                'daily_wages' => 450000,
                'account_number' => '0987654321',
                'bank_id' => 'Mandiri',
                'account_holder_name' => 'Jane Smith',
                'no_bpjstk' => 'BPJSTK002',
                'no_bpjskes' => 'BPJSKES002',
                'employee_id' => $employees->get(1)->id,
                'placement_id' => $placements->get(1)->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
        ];

        foreach ($contracts as $contractData) {
            ContractEmployee::create($contractData);
        }
    }
} 