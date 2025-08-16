<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Placement;
use App\Models\Client;
use App\Models\User;

class PlacementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        $clients = Client::all();

        $placements = [
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Web Development Project',
                'description' => 'Custom web application development for client',
                'client_id' => $clients->first()->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Mobile App Development',
                'description' => 'iOS and Android mobile application development',
                'client_id' => $clients->get(1)->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'System Integration',
                'description' => 'Legacy system integration and API development',
                'client_id' => $clients->get(2)->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
        ];

        foreach ($placements as $placementData) {
            Placement::create($placementData);
        }
    }
} 