<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

class InternalUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Budi Dosen',
            'email' => 'dosen@staba.ac.id',
            'nip_nidn' => '1234567890',
            'phone_number' => '081234567891',
            'password' => Hash::make('password'),
            'role' => UserRole::DOSEN,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Siti Staff',
            'email' => 'staff@staba.ac.id',
            'nip_nidn' => '0987654321',
            'phone_number' => '081234567892',
            'password' => Hash::make('password'),
            'role' => UserRole::STAFF,
            'is_active' => true,
        ]);
    }
}
