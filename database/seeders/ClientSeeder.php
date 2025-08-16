<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();

        $clients = [
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'PT Maju Bersama',
                'logo' => 'clients/logos/maju-bersama-logo.png',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'phone' => '021-555-0123',
                'email' => 'info@majubersama.com',
                'pic_name' => 'Budi Santoso',
                'pic_phone' => '0812-3456-7890',
                'pic_email' => 'budi@majubersama.com',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'CV Sukses Mandiri',
                'logo' => 'clients/logos/sukses-mandiri-logo.jpg',
                'address' => 'Jl. Thamrin No. 456, Jakarta Selatan',
                'phone' => '021-555-0456',
                'email' => 'contact@suksesmandiri.com',
                'pic_name' => 'Siti Rahma',
                'pic_phone' => '0813-4567-8901',
                'pic_email' => 'siti@suksesmandiri.com',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'PT Global Solutions',
                'logo' => 'clients/logos/global-solutions-logo.png',
                'address' => 'Jl. Gatot Subroto No. 789, Jakarta Barat',
                'phone' => '021-555-0789',
                'email' => 'hello@globalsolutions.com',
                'pic_name' => 'Ahmad Rizki',
                'pic_phone' => '0814-5678-9012',
                'pic_email' => 'ahmad@globalsolutions.com',
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }
    }
}
