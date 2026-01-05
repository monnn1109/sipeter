<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();

            // =========================================================
            // BASIC INFO
            // =========================================================
            $table->string('request_code', 50)->unique();
            $table->foreignId('document_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // =========================================================
            // APPLICANT INFO (LENGKAP)
            // =========================================================
            $table->enum('applicant_type', ['mahasiswa', 'dosen', 'staff']);
            $table->string('applicant_name');
            $table->string('applicant_identifier', 50); // NIM/NIP/NIDN
            $table->string('applicant_email');
            $table->string('applicant_phone', 20);
            $table->string('applicant_unit'); // Program Studi / Unit Kerja
            $table->text('applicant_address'); // ✅ ALAMAT LENGKAP

            // =========================================================
            // REQUEST DETAILS
            // =========================================================
            $table->text('purpose'); // Keperluan
            $table->text('notes')->nullable(); // Catatan pemohon
            $table->enum('delivery_method', ['pickup', 'download']); // ✅ BARU

            // =========================================================
            // STATUS & PROCESSING
            // =========================================================
            $table->enum('status', [
                'submitted',        // Baru diajukan (AWAL)
                'pending',          // Menunggu approval
                'approved',         // Disetujui admin
                'processing',       // Sedang diproses
                'ready_for_pickup', // Siap diambil/download
                'picked_up',        // Sudah diambil
                'completed',        // Selesai
                'rejected'          // Ditolak
            ])->default('submitted'); // ✅ DEFAULT: submitted

            $table->text('admin_notes')->nullable(); // Catatan admin
            $table->text('rejection_reason')->nullable(); // Alasan ditolak

            // =========================================================
            // FILE MANAGEMENT
            // =========================================================
            $table->string('file_path')->nullable(); // Path file yang diupload
            $table->timestamp('file_uploaded_at')->nullable(); // Kapan diupload
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null'); // Siapa yang upload

            // =========================================================
            // WORKFLOW TIMESTAMPS
            // =========================================================
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamp('ready_at')->nullable(); // Kapan ready
            $table->timestamp('picked_up_at')->nullable(); // Kapan diambil
            $table->timestamp('completed_at')->nullable(); // Kapan selesai

            $table->timestamps(); // created_at & updated_at

            // =========================================================
            // INDEXES (untuk performa query)
            // =========================================================
            $table->index('request_code');
            $table->index('status');
            $table->index('applicant_type');
            $table->index('applicant_identifier');
            $table->index('delivery_method');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_requests');
    }
};
