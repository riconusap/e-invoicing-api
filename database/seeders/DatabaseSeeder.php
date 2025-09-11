<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ClientSeeder::class,
            EmployeeSeeder::class,
            PicExternalSeeder::class,
            PlacementSeeder::class,
            ContractClientSeeder::class,
            ContractEmployeeSeeder::class,
            InvoiceSeeder::class,
            InvoiceItemSeeder::class,
        ]);
    }
}
