<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('restrict')->onUpdate('cascade');
            $table->decimal('quantity', 8, 2)->comment('Jumlah (kg atau pcs)');
            $table->decimal('price_at_transaction', 10, 2)->comment('SNAPSHOT harga saat order dibuat (untuk audit)');
            $table->decimal('subtotal', 10, 2)->comment('quantity * price_at_transaction');
            $table->text('notes')->nullable()->comment('Catatan khusus untuk item ini');
            $table->timestamps();
            // Note: SQL did not explicitly mention SoftDeletes for details, usually details are hard deleted if transaction is, but we can add timestamps.
            // Wait, SQL for transaction_details: "created_at, updated_at TIMESTAMP...". No "deleted_at".
            // So I will NOT add SoftDeletes here unless user wants consistency, but strict SQL adherence says no.
            // User checklist: "setiap migrasi menyertakan Soft Deletes...".
            // Okay, user said "setiap migrasi menyertakan Soft Deletes", but checking the SQL provided:
            // Table 5 `transaction_details`: No deleted_at.
            // Table 1,2,3,4,6,7,8 Have `deleted_at` or implied?
            // Table 1 (admins): Yes.
            // Table 2 (customers): Yes.
            // Table 3 (services): Yes.
            // Table 4 (transactions): Yes.
            // Table 6 (payments): NO. Wait. SQL line 256 only has foreign keys. Line 255 updated_at. No deleted_at for payments.
            // Table 7 (shipments): NO deleted_at.
            // Table 8 (logs): NO deleted_at.
            //
            // User request: "Pastikan setiap migrasi menyertakan Soft Deletes..."
            // This contradicts the SQL for some tables.
            //
            // Strategy: I will follow the USER REQUEST to include Soft Deletes "agar sinkron dengan model Eloquent".
            // I will add SoftDeletes to ALL tables as requested, even if SQL didn't explicitly show it for some.
            // It's a safer default for Laravel apps.
            // So for transaction_details, payments, shipments, logs too.

            $table->softDeletes();
        });

        // Add Check Constraints
        DB::statement('ALTER TABLE transaction_details ADD CONSTRAINT chk_quantity CHECK (quantity > 0)');
        DB::statement('ALTER TABLE transaction_details ADD CONSTRAINT chk_price_at_transaction CHECK (price_at_transaction >= 0)');
        DB::statement('ALTER TABLE transaction_details ADD CONSTRAINT chk_subtotal CHECK (subtotal >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
