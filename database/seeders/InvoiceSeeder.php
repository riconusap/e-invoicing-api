<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\ContractClient;
use App\Models\User;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        $contracts = ContractClient::all();

        $invoices = [
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'invoice_number' => 'INV-2024-001',
                'invoice_date' => '2024-01-15',
                'due_date' => '2024-01-30',
                'subtotal' => 25000000,
                'tax' => 2500000,
                'total' => 27500000,
                'status' => 'pending',
                'notes' => 'First milestone payment',
                'contract_client_id' => $contracts->first()->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'invoice_number' => 'INV-2024-002',
                'invoice_date' => '2024-02-15',
                'due_date' => '2024-03-01',
                'subtotal' => 37500000,
                'tax' => 3750000,
                'total' => 41250000,
                'status' => 'paid',
                'notes' => 'Second milestone payment',
                'contract_client_id' => $contracts->get(1)->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'invoice_number' => 'INV-2024-003',
                'invoice_date' => '2024-03-15',
                'due_date' => '2024-03-30',
                'subtotal' => 15000000,
                'tax' => 1500000,
                'total' => 16500000,
                'status' => 'overdue',
                'notes' => 'Final payment',
                'contract_client_id' => $contracts->get(2)->id,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
        ];

        foreach ($invoices as $invoiceData) {
            Invoice::create($invoiceData);
        }
    }
} 