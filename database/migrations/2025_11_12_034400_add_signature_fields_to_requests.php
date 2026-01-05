<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // ✅ CEK DULU APAKAH KOLOM SUDAH ADA

            if (!Schema::hasColumn('document_requests', 'requires_signature')) {
                $table->boolean('requires_signature')->default(false)->after('status');
            }

            if (!Schema::hasColumn('document_requests', 'signature_status')) {
                $table->string('signature_status')->nullable()->after('requires_signature');
            }

            if (!Schema::hasColumn('document_requests', 'signature_requested_at')) {
                $table->timestamp('signature_requested_at')->nullable()->after('signature_status');
            }

            if (!Schema::hasColumn('document_requests', 'signature_completed_at')) {
                $table->timestamp('signature_completed_at')->nullable()->after('signature_requested_at');
            }

            if (!Schema::hasColumn('document_requests', 'signature_requested_by')) {
                $table->foreignId('signature_requested_by')
                    ->nullable()
                    ->after('signature_completed_at')
                    ->constrained('users')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('document_requests', 'signatures_required')) {
                $table->integer('signatures_required')->default(0)->after('signature_requested_by');
            }

            if (!Schema::hasColumn('document_requests', 'signatures_completed')) {
                $table->integer('signatures_completed')->default(0)->after('signatures_required');
            }

            // ✅ ADD: current_signature_step (untuk 3-level tracking)
            if (!Schema::hasColumn('document_requests', 'current_signature_step')) {
                $table->tinyInteger('current_signature_step')
                    ->default(0)
                    ->after('signatures_completed')
                    ->comment('0=Not started, 1=Level 1, 2=Level 2, 3=Level 3');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('document_requests', 'signature_requested_by')) {
                $table->dropForeign(['signature_requested_by']);
            }

            if (Schema::hasColumn('document_requests', 'requires_signature')) {
                $columnsToDrop[] = 'requires_signature';
            }
            if (Schema::hasColumn('document_requests', 'signature_status')) {
                $columnsToDrop[] = 'signature_status';
            }
            if (Schema::hasColumn('document_requests', 'signature_requested_at')) {
                $columnsToDrop[] = 'signature_requested_at';
            }
            if (Schema::hasColumn('document_requests', 'signature_completed_at')) {
                $columnsToDrop[] = 'signature_completed_at';
            }
            if (Schema::hasColumn('document_requests', 'signature_requested_by')) {
                $columnsToDrop[] = 'signature_requested_by';
            }
            if (Schema::hasColumn('document_requests', 'signatures_required')) {
                $columnsToDrop[] = 'signatures_required';
            }
            if (Schema::hasColumn('document_requests', 'signatures_completed')) {
                $columnsToDrop[] = 'signatures_completed';
            }
            if (Schema::hasColumn('document_requests', 'current_signature_step')) {
                $columnsToDrop[] = 'current_signature_step';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
