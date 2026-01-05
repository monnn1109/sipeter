<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('document_requests', 'verification_authority_id')) {
                $table->foreignId('verification_authority_id')
                    ->nullable()
                    ->after('approved_by')
                    ->constrained('signature_authorities')
                    ->onDelete('set null')
                    ->comment('Pejabat yang memverifikasi dokumen');
            }

            if (!Schema::hasColumn('document_requests', 'rejection_reason')) {
                $table->text('rejection_reason')
                    ->nullable()
                    ->after('notes')
                    ->comment('Alasan jika dokumen ditolak (verifikasi/TTD)');
            }
        });
    }


    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            if (Schema::hasColumn('document_requests', 'verification_authority_id')) {
                $table->dropForeign(['verification_authority_id']);
            }

            $columnsToDrop = [];
            if (Schema::hasColumn('document_requests', 'verification_authority_id')) {
                $columnsToDrop[] = 'verification_authority_id';
            }
            if (Schema::hasColumn('document_requests', 'rejection_reason')) {
                $columnsToDrop[] = 'rejection_reason';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
