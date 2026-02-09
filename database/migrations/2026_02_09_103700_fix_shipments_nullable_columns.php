<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * DeepFix: Kolom scheduled_at, shipment_type, customer_address tidak selalu diisi
     * saat assignment kurir. Kita ubah jadi nullable.
     */
    public function up(): void
    {
        // DeepFix: Use raw SQL karena Doctrine DBAL tidak support ENUM change
        DB::statement("ALTER TABLE shipments MODIFY COLUMN scheduled_at DATETIME NULL");
        DB::statement("ALTER TABLE shipments MODIFY COLUMN shipment_type ENUM('pickup', 'delivery') NULL DEFAULT 'delivery'");
        DB::statement("ALTER TABLE shipments MODIFY COLUMN customer_address TEXT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY COLUMN scheduled_at DATETIME NOT NULL");
        DB::statement("ALTER TABLE shipments MODIFY COLUMN shipment_type ENUM('pickup', 'delivery') NOT NULL");
        DB::statement("ALTER TABLE shipments MODIFY COLUMN customer_address TEXT NOT NULL");
    }
};
