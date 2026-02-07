<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama pelanggan');
            $table->string('email')->nullable()->unique()->comment('Email untuk notifikasi');
            $table->string('phone_number', 20)->unique()->comment('Nomor telepon (wajib)');
            $table->text('address')->nullable()->comment('Alamat lengkap');
            $table->enum('customer_type', ['individual', 'corporate'])->default('individual')
                ->comment('Tipe pelanggan: perorangan atau korporat');
            $table->timestamp('email_verified_at')->nullable()->comment('Laravel email verification');
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone_number');
            $table->index('customer_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
