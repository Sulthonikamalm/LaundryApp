<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Admin;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Services
        $this->command->info('Creating Demo Services...');
        
        $kiloan = Service::create([
            'service_name' => 'Cuci Kiloan Reguler',
            'service_type' => 'kiloan',
            'unit' => 'kg',
            'base_price' => 7000,
            'estimated_duration_hours' => 48,
            'description' => 'Cuci bersih, setrika, wangi. Selesai 2 hari.',
        ]);

        $express = Service::create([
            'service_name' => 'Cuci Kiloan Express',
            'service_type' => 'express',
            'unit' => 'kg',
            'base_price' => 12000,
            'estimated_duration_hours' => 24,
            'description' => 'Cuci prioritas, selesai 1 hari.',
        ]);

        $satuan = Service::create([
            'service_name' => 'Cuci Bed Cover (Single)',
            'service_type' => 'satuan',
            'unit' => 'pcs',
            'base_price' => 25000,
            'estimated_duration_hours' => 72,
            'description' => 'Cuci satuan khusus bed cover kecil.',
        ]);

        // 2. Create Demo Customer
        $this->command->info('Creating Demo Customer...');
        
        $customer = Customer::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone_number' => '081234567890',
            'address' => 'Jl. Merdeka No. 45, Jakarta Pusat',
            'customer_type' => 'individual',
        ]);

        // 3. Create Demo Transaction (Processed by Owner)
        $this->command->info('Creating Demo Transaction...');

        $owner = Admin::where('role', 'owner')->first();

        // TRX 1: Processing
        $trx1 = Transaction::create([
            'transaction_code' => 'LDR-DEMO-001',
            'customer_id' => $customer->id,
            'created_by' => $owner?->id,
            'order_date' => Carbon::now()->subDay(),
            'estimated_completion_date' => Carbon::now()->addDay(),
            'status' => 'processing',
            'payment_status' => 'paid',
            'customer_notes' => 'Tolong dipisah pakaian putih.',
            'total_cost' => 0, // Will be updated by details
            'total_paid' => 0, // Will be updated manually
        ]);

        // Add details to TRX 1
        TransactionDetail::create([
            'transaction_id' => $trx1->id,
            'service_id' => $kiloan->id,
            'quantity' => 5.5, // 5.5 kg
            // price_at_transaction auto-filled by boot
            // subtotal auto-calculated
        ]);

        // Update Payment (Full Paid)
        $trx1->update([
            'total_paid' => $trx1->fresh()->total_cost // Paid full
        ]);

        // TRX 2: Pending
        $trx2 = Transaction::create([
            'transaction_code' => 'LDR-DEMO-002',
            'customer_id' => $customer->id,
            'created_by' => $owner?->id,
            'order_date' => Carbon::now(),
            'estimated_completion_date' => Carbon::now()->addDays(2),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total_cost' => 0,
            'total_paid' => 0,
        ]);

         TransactionDetail::create([
            'transaction_id' => $trx2->id,
            'service_id' => $satuan->id,
            'quantity' => 1,
        ]);

        $this->command->info('✅ Demo Data Seeded Successfully!');
        $this->command->info('   - Service: 3 Created');
        $this->command->info('   - Customer: Budi Santoso (081234567890)');
        $this->command->info('   - Transaction 1: LDR-DEMO-001 (Processing, Paid)');
        $this->command->info('   - Transaction 2: LDR-DEMO-002 (Pending, Unpaid)');
    }
}
