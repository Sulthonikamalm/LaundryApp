<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Membuat akun admin default untuk pertama kali.
     */
    public function run(): void
    {
        // Owner Account (Super Admin)
        Admin::firstOrCreate(
            ['email' => 'owner@laundry.com'],
            [
                'name' => 'Owner Laundry',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'phone_number' => '081234567890',
                'is_active' => true,
            ]
        );

        // Kasir Account
        Admin::firstOrCreate(
            ['email' => 'kasir@laundry.com'],
            [
                'name' => 'Kasir Utama',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'phone_number' => '081234567891',
                'is_active' => true,
            ]
        );

        // Courier Account (Login via PIN, bukan email/password di panel)
        Admin::firstOrCreate(
            ['email' => 'courier@laundry.com'],
            [
                'name' => 'Kurir Utama',
                'password' => Hash::make('password123'),
                'role' => 'courier',
                'phone_number' => '081234567892',
                'is_active' => true,
                'pin' => Hash::make('123456'), // PIN 6 digit untuk quick login
            ]
        );

        $this->command->info('✅ Admin accounts seeded successfully!');
        $this->command->info('   Owner: owner@laundry.com / password123');
        $this->command->info('   Kasir: kasir@laundry.com / password123');
        $this->command->info('   Courier: courier@laundry.com (PIN: 123456)');
    }
}
