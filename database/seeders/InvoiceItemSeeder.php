<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Models\User;

class InvoiceItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        $invoices = Invoice::all();

        $invoiceItems = [
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'description' => 'Frontend Development',
                'quantity' => 1,
                'unit_price' => 15000000,
                'total' => 15000000,
                'invoice_id' => $invoices->first()->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'description' => 'Backend Development',
                'quantity' => 1,
                'unit_price' => 10000000,
                'total' => 10000000,
                'invoice_id' => $invoices->first()->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'description' => 'Mobile App Development',
                'quantity' => 1,
                'unit_price' => 25000000,
                'total' => 25000000,
                'invoice_id' => $invoices->get(1)->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'description' => 'UI/UX Design',
                'quantity' => 1,
                'unit_price' => 12500000,
                'total' => 12500000,
                'invoice_id' => $invoices->get(1)->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'description' => 'System Integration',
                'quantity' => 1,
                'unit_price' => 15000000,
                'total' => 15000000,
                'invoice_id' => $invoices->get(2)->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
        ];

        foreach ($invoiceItems as $itemData) {
            InvoiceItem::create($itemData);
        }
    }
}
