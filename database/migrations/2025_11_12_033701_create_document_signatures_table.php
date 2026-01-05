<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('signature_authority_id')->constrained()->cascadeOnDelete();

            // 🔥 NEW: Signature level (1, 2, 3)
            $table->unsignedTinyInteger('signature_level')->default(1);

            // File TTD
            $table->string('signature_file')->nullable();
            $table->string('qr_code_file')->nullable(); // 🔥 NEW: QR Code file
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();

            // Status & Verification
            $table->enum('status', [
                'requested',
                'uploaded',
                'verified',
                'rejected'
            ])->default('requested');

            // Timestamps penting
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // Verifikasi oleh admin
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->text('verification_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Metadata
            $table->string('uploaded_from')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
        });

        // Index untuk performa
        Schema::table('document_signatures', function (Blueprint $table) {
            $table->index('document_request_id');
            $table->index('signature_authority_id');
            $table->index('status');
            $table->index('signature_level'); // 🔥 NEW
            $table->index(['document_request_id', 'status']);
            $table->index(['document_request_id', 'signature_level']); // 🔥 NEW
            $table->index(['signature_level', 'status']); // 🔥 NEW
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_signatures');
    }
};
