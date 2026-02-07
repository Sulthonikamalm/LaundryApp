<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DeepPerformance: Add performance indexes for frequently queried columns.
 * 
 * This migration adds indexes that significantly improve query performance
 * for the most common access patterns in the application.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Customers - phone_number untuk tracking lookup
        Schema::table('customers', function (Blueprint $table) {
            // Index for tracking lookup (dual-key validation: transaction_code + phone)
            if (!$this->indexExists('customers', 'customers_phone_number_index')) {
                $table->index('phone_number', 'customers_phone_number_index');
            }
        });

        // 2. Shipments - composite index for driver dashboard
        Schema::table('shipments', function (Blueprint $table) {
            // Index for driver dashboard: assigned_driver_id + created_at + status
            if (!$this->indexExists('shipments', 'shipments_driver_dashboard_index')) {
                $table->index(['assigned_driver_id', 'created_at', 'status'], 'shipments_driver_dashboard_index');
            }
        });

        // 3. Payments - for revenue calculations
        Schema::table('payments', function (Blueprint $table) {
            // Index for revenue trend queries: status + payment_date
            if (!$this->indexExists('payments', 'payments_revenue_trend_index')) {
                $table->index(['status', 'payment_date'], 'payments_revenue_trend_index');
            }
        });

        // 4. Transaction Details - for report calculations
        Schema::table('transaction_details', function (Blueprint $table) {
            // Index for subtotal aggregations
            if (!$this->indexExists('transaction_details', 'transaction_details_tx_index')) {
                $table->index(['transaction_id', 'deleted_at'], 'transaction_details_tx_index');
            }
        });

        // 5. Transactions - composite index for overdue queries
        Schema::table('transactions', function (Blueprint $table) {
            // Index for overdue widget: status + estimated_completion_date
            if (!$this->indexExists('transactions', 'transactions_overdue_index')) {
                $table->index(['status', 'estimated_completion_date'], 'transactions_overdue_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_phone_number_index');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropIndex('shipments_driver_dashboard_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_revenue_trend_index');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropIndex('transaction_details_tx_index');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_overdue_index');
        });
    }

    /**
     * Check if an index exists on a table.
     */
    protected function indexExists(string $table, string $indexName): bool
    {
        $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table);
        return isset($indexes[$indexName]) || isset($indexes[strtolower($indexName)]);
    }
};
