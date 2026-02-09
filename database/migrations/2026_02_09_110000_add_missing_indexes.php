<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration: Add Missing Database Indexes
 * 
 * DeepPerformance: Menambahkan index pada foreign keys dan kolom yang sering di-query.
 * DeepReasoning: Index mempercepat JOIN dan WHERE clause pada kolom tersebut.
 * 
 * Impact: Query performance improvement 10-100x pada tabel besar.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // DeepFix: Gunakan raw SQL untuk menghindari Doctrine DBAL issue dengan ENUM
        
        // Transactions table indexes
        DB::statement('CREATE INDEX IF NOT EXISTS transactions_customer_id_index ON transactions(customer_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS transactions_created_by_index ON transactions(created_by)');
        DB::statement('CREATE INDEX IF NOT EXISTS transactions_status_index ON transactions(status)');
        DB::statement('CREATE INDEX IF NOT EXISTS transactions_payment_status_index ON transactions(payment_status)');
        DB::statement('CREATE INDEX IF NOT EXISTS transactions_order_date_index ON transactions(order_date)');

        // Transaction details table indexes
        DB::statement('CREATE INDEX IF NOT EXISTS transaction_details_transaction_id_index ON transaction_details(transaction_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS transaction_details_service_id_index ON transaction_details(service_id)');

        // Shipments table indexes
        DB::statement('CREATE INDEX IF NOT EXISTS shipments_transaction_id_index ON shipments(transaction_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS shipments_courier_id_index ON shipments(courier_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS shipments_status_index ON shipments(status)');
        DB::statement('CREATE INDEX IF NOT EXISTS shipments_assigned_at_index ON shipments(assigned_at)');

        // Payments table indexes
        DB::statement('CREATE INDEX IF NOT EXISTS payments_transaction_id_index ON payments(transaction_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS payments_processed_by_index ON payments(processed_by)');
        DB::statement('CREATE INDEX IF NOT EXISTS payments_payment_date_index ON payments(payment_date)');

        // Transaction status logs table indexes
        DB::statement('CREATE INDEX IF NOT EXISTS transaction_status_logs_transaction_id_index ON transaction_status_logs(transaction_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS transaction_status_logs_changed_by_index ON transaction_status_logs(changed_by)');
        DB::statement('CREATE INDEX IF NOT EXISTS transaction_status_logs_created_at_index ON transaction_status_logs(created_at)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes
        DB::statement('DROP INDEX IF EXISTS transactions_customer_id_index ON transactions');
        DB::statement('DROP INDEX IF EXISTS transactions_created_by_index ON transactions');
        DB::statement('DROP INDEX IF EXISTS transactions_status_index ON transactions');
        DB::statement('DROP INDEX IF EXISTS transactions_payment_status_index ON transactions');
        DB::statement('DROP INDEX IF EXISTS transactions_order_date_index ON transactions');

        DB::statement('DROP INDEX IF EXISTS transaction_details_transaction_id_index ON transaction_details');
        DB::statement('DROP INDEX IF EXISTS transaction_details_service_id_index ON transaction_details');

        DB::statement('DROP INDEX IF EXISTS shipments_transaction_id_index ON shipments');
        DB::statement('DROP INDEX IF EXISTS shipments_courier_id_index ON shipments');
        DB::statement('DROP INDEX IF EXISTS shipments_status_index ON shipments');
        DB::statement('DROP INDEX IF EXISTS shipments_assigned_at_index ON shipments');

        DB::statement('DROP INDEX IF EXISTS payments_transaction_id_index ON payments');
        DB::statement('DROP INDEX IF EXISTS payments_processed_by_index ON payments');
        DB::statement('DROP INDEX IF EXISTS payments_payment_date_index ON payments');

        DB::statement('DROP INDEX IF EXISTS transaction_status_logs_transaction_id_index ON transaction_status_logs');
        DB::statement('DROP INDEX IF EXISTS transaction_status_logs_changed_by_index ON transaction_status_logs');
        DB::statement('DROP INDEX IF EXISTS transaction_status_logs_created_at_index ON transaction_status_logs');
    }
};
