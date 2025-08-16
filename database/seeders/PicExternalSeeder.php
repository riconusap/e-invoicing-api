<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PicExternal;
use App\Models\User;

class PicExternalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();

        $picExternals = [
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Sarah Wilson',
                'position' => 'Project Manager',
                'phone' => '0815-6789-0123',
                'email' => 'sarah.wilson@external.com',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Mike Davis',
                'position' => 'Technical Lead',
                'phone' => '0816-7890-1234',
                'email' => 'mike.davis@external.com',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Lisa Garcia',
                'position' => 'Business Analyst',
                'phone' => '0817-8901-2345',
                'email' => 'lisa.garcia@external.com',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
        ];

        foreach ($picExternals as $picData) {
            PicExternal::create($picData);
        }
    }
} 