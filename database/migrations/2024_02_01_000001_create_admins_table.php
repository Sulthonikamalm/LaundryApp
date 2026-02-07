<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama lengkap admin');
            $table->string('email')->unique()->comment('Email untuk login');
            $table->string('password')->comment('Password hashed dengan bcrypt');
            $table->enum('role', ['owner', 'kasir', 'courier'])->comment('Role akses sistem');
            $table->string('phone_number', 20)->nullable()->comment('Nomor telepon');
            $table->boolean('is_active')->default(true)->comment('Status aktif/nonaktif');
            $table->string('pin')->nullable()->comment('PIN untuk courier (hashed), 6 digit');
            $table->timestamp('last_login_at')->nullable()->comment('Track login terakhir');
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at

            $table->index('email');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
