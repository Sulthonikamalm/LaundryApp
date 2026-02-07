<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('courier_id')->nullable()->constrained('admins')->onDelete('set null')->onUpdate('cascade');
            $table->enum('shipment_type', ['pickup', 'delivery'])->comment('Tipe pengiriman');
            $table->dateTime('scheduled_at')->comment('Jadwal pengiriman');
            $table->dateTime('completed_at')->nullable()->comment('Waktu selesai aktual');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'failed'])->default('scheduled')->comment('Status pengiriman');
            $table->text('customer_address')->comment('Alamat lengkap customer');
            $table->string('photo_proof_url')->nullable()->comment('Cloudinary URL foto bukti pengiriman');
            $table->text('notes')->nullable()->comment('Catatan pengiriman');
            $table->timestamps();
            $table->softDeletes(); // Added as per user request

            $table->index('shipment_type');
            $table->index('status');
            $table->index('scheduled_at');
            // Composite index
            $table->index(['courier_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
