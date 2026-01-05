<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('🗑️ Clearing existing document types...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DocumentType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $documentTypes = [
            // DOKUMEN UNTUK MAHASISWA
            [
                'name' => 'Surat Keterangan Aktif Kuliah',
                'code_prefix' => 'SKAK',
                'description' => 'Surat keterangan yang menyatakan mahasiswa aktif kuliah',
                'applicable_for' => ['mahasiswa'],  
                'processing_days' => 3,
                'required_fields' => ['nim', 'nama', 'program_studi', 'semester'],
                'is_active' => true,
            ],
            [
                'name' => 'Surat Keterangan Lulus',
                'code_prefix' => 'SKL',
                'description' => 'Surat keterangan kelulusan mahasiswa',
                'applicable_for' => ['mahasiswa'],
                'processing_days' => 5,
                'required_fields' => ['nim', 'nama', 'program_studi', 'tanggal_lulus'],
                'is_active' => true,
            ],
            [
                'name' => 'Transkrip Nilai',
                'code_prefix' => 'TN',
                'description' => 'Dokumen yang berisi daftar nilai mata kuliah',
                'applicable_for' => ['mahasiswa'],
                'processing_days' => 7,
                'required_fields' => ['nim', 'nama', 'program_studi'],
                'is_active' => true,
            ],
            [
                'name' => 'Surat Pengantar Penelitian',
                'code_prefix' => 'SPP',
                'description' => 'Surat pengantar untuk keperluan penelitian skripsi/tugas akhir',
                'applicable_for' => ['mahasiswa'],
                'processing_days' => 2,
                'required_fields' => ['nim', 'nama', 'program_studi', 'judul_penelitian'],
                'is_active' => true,
            ],
            [
                'name' => 'Surat Keterangan Bebas Pustaka',
                'code_prefix' => 'SKBP',
                'description' => 'Surat keterangan tidak memiliki tanggungan perpustakaan',
                'applicable_for' => ['mahasiswa'],
                'processing_days' => 3,
                'required_fields' => ['nim', 'nama', 'program_studi'],
                'is_active' => true,
            ],
            [
                'name' => 'Legalisir Ijazah',
                'code_prefix' => 'LEG',
                'description' => 'Legalisir dokumen ijazah resmi',
                'applicable_for' => ['mahasiswa'],
                'processing_days' => 3,
                'required_fields' => ['nim', 'nama', 'program_studi'],
                'is_active' => true,
            ],

            // DOKUMEN UNTUK DOSEN & STAFF (Internal)
            [
                'name' => 'Surat Izin Penelitian',
                'code_prefix' => 'SIP',
                'description' => 'Surat izin untuk melakukan penelitian',
                'applicable_for' => ['dosen', 'staff'],  // ✅ LANGSUNG ARRAY
                'processing_days' => 3,
                'required_fields' => ['nip', 'nama', 'unit'],
                'is_active' => true,
            ],
            [
                'name' => 'Surat Tugas',
                'code_prefix' => 'ST',
                'description' => 'Surat tugas untuk kegiatan dinas',
                'applicable_for' => ['dosen', 'staff'],
                'processing_days' => 2,
                'required_fields' => ['nip', 'nama', 'unit'],
                'is_active' => true,
            ],
            [
                'name' => 'Surat Keterangan Mengajar',
                'code_prefix' => 'SKM',
                'description' => 'Surat keterangan aktif mengajar',
                'applicable_for' => ['dosen'],
                'processing_days' => 2,
                'required_fields' => ['nip', 'nama', 'unit'],
                'is_active' => true,
            ],
            [
                'name' => 'Surat Keterangan Kerja',
                'code_prefix' => 'SKK',
                'description' => 'Surat keterangan masih bekerja sebagai staff',
                'applicable_for' => ['staff'],
                'processing_days' => 2,
                'required_fields' => ['nip', 'nama', 'unit'],
                'is_active' => true,
            ],
        ];

        $successCount = 0;
        $errorCount = 0;

        foreach ($documentTypes as $type) {
            try {
                $created = DocumentType::create($type);
                $successCount++;

                Log::info("✅ Created: {$type['name']}", [
                    'id' => $created->id,
                    'code' => $type['code_prefix']
                ]);

            } catch (\Exception $e) {
                $errorCount++;

                Log::error("❌ Failed: {$type['name']}", [
                    'error' => $e->getMessage(),
                    'data' => $type
                ]);
            }
        }

        $totalCount = DocumentType::count();

        $this->command->info("✅ Document Types Seeding Completed!");
        $this->command->info("   - Success: {$successCount}");
        $this->command->info("   - Errors: {$errorCount}");
        $this->command->info("   - Total in DB: {$totalCount}");

        if ($totalCount === 0) {
            $this->command->error("⚠️ WARNING: No document types in database!");
        }
    }
}
