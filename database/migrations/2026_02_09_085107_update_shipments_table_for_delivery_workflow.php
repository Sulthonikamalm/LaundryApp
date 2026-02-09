<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * DeepReasoning: Update shipments workflow untuk support delivery assignment
     * - assigned_at: Timestamp saat kurir ditugaskan
     * - Update status enum: pending, picked_up, delivered, cancelled
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Add assigned_at timestamp
            if (!Schema::hasColumn('shipments', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('courier_id');
            }
        });
        
        // DeepFix: Update status enum values
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN status ENUM('pending', 'picked_up', 'delivered', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'assigned_at')) {
                $table->dropColumn('assigned_at');
            }
        });
        
        // Revert status enum
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN status ENUM('scheduled', 'in_progress', 'completed', 'failed') DEFAULT 'scheduled'");
    }
};
