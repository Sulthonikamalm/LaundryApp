<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * DeepReasoning: Flagging system untuk membedakan transaksi pickup vs delivery.
     * - is_delivery: Boolean flag untuk trigger delivery workflow
     * - delivery_cost: Biaya ongkir yang bisa berbeda per transaksi (jarak, urgency)
     * - delivery_address: Override alamat customer jika berbeda dari alamat utama
     */
    public function up(): void
    {
        // DeepFix: Add columns one by one to avoid "after" reference issues
        if (!Schema::hasColumn('transactions', 'is_delivery')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->boolean('is_delivery')->default(false)->after('payment_status');
            });
        }
        
        if (!Schema::hasColumn('transactions', 'delivery_cost')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->decimal('delivery_cost', 10, 2)->default(0)->after('is_delivery');
            });
        }
        
        if (!Schema::hasColumn('transactions', 'delivery_address')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->text('delivery_address')->nullable()->after('delivery_cost');
            });
        }
        
        // DeepPerformance: Index untuk query filtering "transaksi yang perlu diantar"
        if (!$this->indexExists('transactions', 'transactions_is_delivery_status_index')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->index(['is_delivery', 'status'], 'transactions_is_delivery_status_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if ($this->indexExists('transactions', 'transactions_is_delivery_status_index')) {
                $table->dropIndex('transactions_is_delivery_status_index');
            }
            
            $columns = ['delivery_address', 'delivery_cost', 'is_delivery'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('transactions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
    
    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        try {
            $connection = Schema::getConnection();
            $indexes = $connection->getDoctrineSchemaManager()
                ->listTableIndexes($table);
            
            return array_key_exists($index, $indexes);
        } catch (\Exception $e) {
            return false;
        }
    }
};
