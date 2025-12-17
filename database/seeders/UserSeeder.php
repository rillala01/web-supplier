<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun buat Big Boss (Admin)
        // Bisa akses semua menu termasuk Penjualan & Laporan
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Kuncinya di email biar gak duplikat
            [
                'name' => 'Super Admin',
                'role' => User::ROLE_ADMIN, 
                'password' => Hash::make('password'), // Password default: password
                'email_verified_at' => now(),
            ]
        );

        // 2. Akun buat Tim Dapur (Produksi)
        // Cuma bisa akses menu Produksi, gak bisa liat duit/penjualan
        User::updateOrCreate(
            ['email' => 'produksi@gmail.com'],
            [
                'name' => 'Staff Produksi',
                'role' => User::ROLE_PRODUKSI,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        // 3. (Opsional) Akun cadangan buat testing
        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager Operasional',
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}