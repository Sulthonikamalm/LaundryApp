<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('changed_by')->constrained('admins')->onDelete('restrict')->onUpdate('cascade');
            $table->string('previous_status', 50)->nullable()->comment('Status sebelum diubah');
            $table->string('new_status', 50)->comment('Status baru setelah diubah');
            $table->text('notes')->nullable()->comment('Alasan perubahan status');
            // created_at acts as the timestamp for the log
            $table->timestamps();
            $table->softDeletes(); // Added as per user request

            // Indexes
            $table->index('created_at'); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_status_logs');
    }
};
