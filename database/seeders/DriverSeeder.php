<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Courier Account
        if (!Admin::where('username', 'driver1')->exists()) {
            Admin::create([
                'name' => 'Budi Kurir',
                'email' => 'driver1@laundry.com',
                'username' => 'driver1',
                'password' => Hash::make('password'), // Optional fallback
                'pin' => Hash::make('123456'), // PIN: 123456
                'role' => 'courier',
                'phone_number' => '087712345678',
                'is_active' => true,
            ]);
            $this->command->info('Acc created: driver1 / PIN: 123456');
        }
    }
}
