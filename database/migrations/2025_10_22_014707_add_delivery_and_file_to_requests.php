<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tabel 'document_requests' ada sebelum diubah
        if (Schema::hasTable('document_requests')) {
            Schema::table('document_requests', function (Blueprint $table) {

                // Cek jika kolom BELUM ADA, baru tambahkan
                if (!Schema::hasColumn('document_requests', 'delivery_method')) {
                    $table->string('delivery_method', 20)
                        ->default('pickup')
                        ->after('notes');
                }

                if (!Schema::hasColumn('document_requests', 'file_path')) {
                    $table->string('file_path')->nullable()->after('delivery_method');
                }

                if (!Schema::hasColumn('document_requests', 'file_uploaded_at')) {
                    $table->timestamp('file_uploaded_at')->nullable()->after('file_path');
                }

                if (!Schema::hasColumn('document_requests', 'uploaded_by')) {
                    $table->unsignedBigInteger('uploaded_by')->nullable()->after('file_uploaded_at');

                    // Add foreign key for uploaded_by
                    $table->foreign('uploaded_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('document_requests')) {
            Schema::table('document_requests', function (Blueprint $table) {
                // Hapus foreign key dulu jika ada
                if (Schema::hasColumn('document_requests', 'uploaded_by')) {
                    // Nama foreign key default: [tabel]_[kolom]_foreign
                    $table->dropForeign(['uploaded_by']);
                }

                // Hapus kolom-kolom
                $table->dropColumn([
                    'delivery_method',
                    'file_path',
                    'file_uploaded_at',
                    'uploaded_by'
                ]);
            });
        }
    }
};
