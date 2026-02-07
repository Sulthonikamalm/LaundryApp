<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name')->comment('Nama layanan (e.g., Cuci Kiloan)');
            $table->enum('service_type', ['kiloan', 'satuan', 'express'])->comment('Tipe layanan');
            $table->string('unit', 50)->comment('Satuan: kg atau pcs');
            $table->decimal('base_price', 10, 2)->comment('Harga current (akan di-snapshot)');
            $table->integer('estimated_duration_hours')->nullable()->comment('Estimasi durasi pengerjaan (jam)');
            $table->text('description')->nullable()->comment('Deskripsi layanan');
            $table->boolean('is_active')->default(true)->comment('Status aktif/nonaktif');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('service_type');
            $table->index('is_active');
        });

         // Add Check Constraint manually as Blueprint doesn't support it directly in all drivers consistently, 
         // but we can try to use raw SQL for TiDB compatibility and robustness.
         DB::statement('ALTER TABLE services ADD CONSTRAINT chk_base_price CHECK (base_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
