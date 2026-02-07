<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Role Column
            if (!Schema::hasColumn('admins', 'role')) {
                $table->enum('role', ['owner', 'kasir', 'courier'])->default('kasir')->after('email');
            }
            
            // Username Column (for driver login)
            if (!Schema::hasColumn('admins', 'username')) {
                $table->string('username')->unique()->nullable()->after('name');
            }

            // Phone Number
            if (!Schema::hasColumn('admins', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('username');
            }

            // Active Status
            if (!Schema::hasColumn('admins', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('password');
            }

            // PIN (for driver login) - Hashed
            if (!Schema::hasColumn('admins', 'pin')) {
                $table->string('pin')->nullable()->after('is_active');
            }

            // Last Login
            if (!Schema::hasColumn('admins', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['role', 'username', 'phone_number', 'is_active', 'pin', 'last_login_at']);
        });
    }
};
