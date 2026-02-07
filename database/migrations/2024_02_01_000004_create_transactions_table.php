<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 20)->unique()->comment('Kode tracking unik (e.g., LDR-2026-0001)');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('created_by')->nullable()->constrained('admins')->onDelete('set null')->onUpdate('cascade');
            $table->date('order_date')->comment('Tanggal order dibuat');
            $table->date('estimated_completion_date')->nullable()->comment('Estimasi selesai');
            $table->date('actual_completion_date')->nullable()->comment('Tanggal aktual selesai');
            $table->decimal('total_cost', 10, 2)->default(0)->comment('Total biaya (computed dari transaction_details)');
            $table->decimal('total_paid', 10, 2)->default(0)->comment('Total yang sudah dibayar (sum dari payments)');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->comment('Status pembayaran');
            $table->enum('status', ['pending', 'processing', 'ready', 'completed', 'cancelled'])->default('pending')
                ->comment('Status order: pending -> processing -> ready -> completed (atau cancelled)');
            $table->text('customer_notes')->nullable()->comment('Catatan dari customer');
            $table->text('internal_notes')->nullable()->comment('Catatan internal staff');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('payment_status');
            $table->index('order_date');
            $table->index('created_at');
        });

        // Add Check Constraints
        DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_total_cost CHECK (total_cost >= 0)');
        DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_total_paid CHECK (total_paid >= 0)');
        DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_total_paid_not_exceed CHECK (total_paid <= total_cost)');
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
