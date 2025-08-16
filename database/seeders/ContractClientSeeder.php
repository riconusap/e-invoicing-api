<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractClient;
use App\Models\Placement;
use App\Models\User;

class ContractClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        $placements = Placement::all();

        $contracts = [
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'placement_id' => $placements->first()->id,
                'contract_value' => 50000000,
                'start_on' => '2024-01-01',
                'ends_on' => '2024-06-30',
                'project_type' => 'Development',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'placement_id' => $placements->get(1)->id,
                'contract_value' => 75000000,
                'start_on' => '2024-02-01',
                'ends_on' => '2024-08-31',
                'project_type' => 'Development',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'placement_id' => $placements->get(2)->id,
                'contract_value' => 30000000,
                'start_on' => '2024-03-01',
                'ends_on' => '2024-05-31',
                'project_type' => 'Integration',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
        ];

        foreach ($contracts as $contractData) {
            ContractClient::create($contractData);
        }
    }
} 