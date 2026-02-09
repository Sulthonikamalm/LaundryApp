<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * DeepThinking: Mengubah status log dari "statis" menjadi "visual stream".
     * Setiap log bisa punya foto bukti aktivitas.
     */
    public function up(): void
    {
        Schema::table('transaction_status_logs', function (Blueprint $table) {
            // DeepUI: Activity type untuk kategorisasi visual
            $table->string('activity_type', 50)->nullable()->after('new_status')
                ->comment('Jenis aktivitas: washing, drying, ironing, packing, quality_check, etc.');
        });
        
        Schema::table('transaction_status_logs', function (Blueprint $table) {
            // DeepVisual: URL foto bukti aktivitas
            $table->string('photo_url')->nullable()->after('activity_type')
                ->comment('Cloudinary URL untuk foto bukti aktivitas');
        });
        
        Schema::table('transaction_status_logs', function (Blueprint $table) {
            // DeepUX: Flag untuk highlight aktivitas penting
            $table->boolean('is_milestone')->default(false)->after('photo_url')
                ->comment('True jika ini milestone penting (untuk highlight di timeline)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_status_logs', function (Blueprint $table) {
            $table->dropColumn(['activity_type', 'photo_url', 'is_milestone']);
        });
    }
};
