<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('processed_by')->nullable()->constrained('admins')->onDelete('set null')->onUpdate('cascade');
            $table->decimal('amount', 10, 2)->comment('Jumlah pembayaran');
            $table->enum('payment_method', ['cash', 'transfer', 'card', 'ewallet'])->comment('Metode pembayaran');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending')->comment('Status pembayaran');
            $table->string('payment_proof_url')->nullable()->comment('Cloudinary URL untuk bukti transfer');
            $table->string('transaction_reference', 100)->nullable()->comment('Reference dari bank/Midtrans');
            $table->text('notes')->nullable()->comment('Catatan pembayaran');
            $table->timestamp('payment_date')->comment('Tanggal pembayaran dilakukan');
            $table->timestamps();
            $table->softDeletes(); // Added as per user request (though not in SQL)

            $table->index('payment_method');
            $table->index('status');
        });

        // Add Check Constraints
        DB::statement('ALTER TABLE payments ADD CONSTRAINT chk_amount CHECK (amount >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
