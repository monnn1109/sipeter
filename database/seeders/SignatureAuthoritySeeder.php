<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SignatureAuthority;
use App\Enums\AuthorityType;

class SignatureAuthoritySeeder extends Seeder
{
    public function run(): void
    {
        $authorities = [
            // ========================================
            // LEVEL 1: KETUA AKADEMIK (EXISTING)
            // ========================================
            [
                'name' => env('WHATSAPP_KETUA_AKADEMIK_NAME', 'Tubagus Riko Rivanthio, M.Kom'),
                'position' => 'Ketua Bagian Akademik',
                'phone' => env('WHATSAPP_KETUA_AKADEMIK_PHONE', '6282295837826'),
                'email' => 'akademik@staba.ac.id',
                'is_active' => true,
                'authority_type' => AuthorityType::KETUA_AKADEMIK->value,
                'notes' => 'Verifikasi kelayakan akademik mahasiswa (Level 1)',
            ],

            // ========================================
            // LEVEL 2: WAKIL KETUA 3 (KEMAHASISWAAN)
            // ========================================
            [
                'name' => env('WHATSAPP_WAKIL_KETUA_3_NAME', 'Dr. Firman Solihat, Drs., M.T'),
                'position' => 'Wakil Ketua 3 - Bidang Kemahasiswaan',
                'phone' => env('WHATSAPP_WAKIL_KETUA_3_PHONE', '6281563304503'),
                'email' => 'wakilketua3@staba.ac.id',
                'is_active' => true,
                'authority_type' => AuthorityType::WAKIL_KETUA_3->value,
                'notes' => 'Verifikasi kemahasiswaan oleh Wakil Direktur 3 (Level 2)',
            ],

            // ========================================
            // LEVEL 3: KETUA/DIREKTUR (FINAL)
            // ========================================
            [
                'name' => env('WHATSAPP_KETUA_NAME', 'Rani Handriani, S.Si., M.Kes'),
                'position' => 'Direktur',
                'phone' => env('WHATSAPP_KETUA_PHONE', '6281317735296'),
                'email' => 'ketua@staba.ac.id',
                'is_active' => true,
                'authority_type' => AuthorityType::KETUA->value,
                'notes' => 'Approval final dari Direktur (Level 3)',
            ],
        ];

        foreach ($authorities as $authority) {
            SignatureAuthority::updateOrCreate(
                ['email' => $authority['email']], // Check by email (unique)
                $authority
            );
        }

        $this->command->info('✅ Signature Authorities seeded successfully!');
        $this->command->info('📊 Total: ' . count($authorities) . ' authorities');
        $this->command->info('');
        $this->command->line('📋 Authority List:');
        $this->command->line('   1. Ketua Akademik (Level 1)');
        $this->command->line('   2. Wakil Ketua 3 - Kemahasiswaan (Level 2)');
        $this->command->line('   3. Direktur (Level 3 - Final)');
        $this->command->info('');
        $this->command->warn('⚠️  IMPORTANT: Update .env file with correct WhatsApp numbers!');
        $this->command->line('   WHATSAPP_KETUA_AKADEMIK_NAME="..."');
        $this->command->line('   WHATSAPP_KETUA_AKADEMIK_PHONE="628xxx"');
        $this->command->line('   WHATSAPP_WAKIL_KETUA_3_NAME="..."');
        $this->command->line('   WHATSAPP_WAKIL_KETUA_3_PHONE="628xxx"');
        $this->command->line('   WHATSAPP_KETUA_NAME="..."');
        $this->command->line('   WHATSAPP_KETUA_PHONE="628xxx"');
    }
}
