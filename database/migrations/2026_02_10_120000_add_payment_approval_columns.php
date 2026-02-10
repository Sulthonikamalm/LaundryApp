<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * DeepPayment: Support dual-mode payment (demo + midtrans)
     * DeepAudit: Track approval workflow untuk demo mode
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Gateway provider tracking
            $table->string('gateway_provider', 20)->default('demo')->after('payment_method');
            
            // Approval workflow (for demo mode)
            $table->enum('gateway_status', ['pending', 'approved', 'rejected', 'completed'])
                ->default('pending')
                ->after('status');
            
            // Audit trail
            $table->foreignId('approved_by')->nullable()->after('processed_by')->constrained('admins')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');
            
            // Performance index
            $table->index(['gateway_status', 'created_at'], 'idx_gateway_status_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_gateway_status_created');
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'gateway_provider',
                'gateway_status',
                'approved_by',
                'approved_at',
                'rejection_reason',
            ]);
        });
    }
};
