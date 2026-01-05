<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('signature_authorities', 'authority_type')) {
            Schema::table('signature_authorities', function (Blueprint $table) {
                $table->dropColumn('authority_type');
            });
        }

        // Recreate with new values (3-level)
        Schema::table('signature_authorities', function (Blueprint $table) {
            $table->enum('authority_type', [
                'ketua_akademik',
                'wakil_ketua_3',
                'ketua'
            ])->after('name')->comment('Tipe pejabat: Ketua Akademik, Wakil Ketua 3, atau Ketua');
        });

        // =========================================================
        // 2. ADD verification_level to document_verifications
        // =========================================================

        if (!Schema::hasColumn('document_verifications', 'verification_level')) {
            Schema::table('document_verifications', function (Blueprint $table) {
                $table->tinyInteger('verification_level')
                    ->default(1)
                    ->after('authority_id')
                    ->comment('Level verifikasi: 1=Akademik, 2=Wakil Ketua 3, 3=Ketua');
            });
        }

        // Add index (dengan check)
        if (!$this->indexExists('document_verifications', 'document_verifications_verification_level_index')) {
            Schema::table('document_verifications', function (Blueprint $table) {
                $table->index('verification_level');
            });
        }

        // =========================================================
        // 3. ADD current_verification_step to document_requests
        // =========================================================

        if (!Schema::hasColumn('document_requests', 'current_verification_step')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->tinyInteger('current_verification_step')
                    ->default(0)
                    ->after('verification_authority_id')
                    ->comment('Step verifikasi saat ini: 0=Belum, 1=Akademik, 2=Wakil Ketua 3, 3=Ketua');
            });
        }

        // Add index (dengan check)
        if (!$this->indexExists('document_requests', 'document_requests_current_verification_step_index')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->index('current_verification_step');
            });
        }
    }

    public function down(): void
    {
        // Drop document_requests.current_verification_step
        if ($this->indexExists('document_requests', 'document_requests_current_verification_step_index')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->dropIndex(['current_verification_step']);
            });
        }

        if (Schema::hasColumn('document_requests', 'current_verification_step')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->dropColumn('current_verification_step');
            });
        }

        // Drop document_verifications.verification_level
        if ($this->indexExists('document_verifications', 'document_verifications_verification_level_index')) {
            Schema::table('document_verifications', function (Blueprint $table) {
                $table->dropIndex(['verification_level']);
            });
        }

        if (Schema::hasColumn('document_verifications', 'verification_level')) {
            Schema::table('document_verifications', function (Blueprint $table) {
                $table->dropColumn('verification_level');
            });
        }

        // Revert signature_authorities.authority_type to 2-level
        if (Schema::hasColumn('signature_authorities', 'authority_type')) {
            Schema::table('signature_authorities', function (Blueprint $table) {
                $table->dropColumn('authority_type');
            });
        }

        Schema::table('signature_authorities', function (Blueprint $table) {
            $table->enum('authority_type', ['academic', 'student_affairs'])
                ->after('name');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();

        $result = DB::select(
            "SELECT COUNT(*) as count
             FROM information_schema.statistics
             WHERE table_schema = ?
             AND table_name = ?
             AND index_name = ?",
            [$databaseName, $table, $indexName]
        );

        return $result[0]->count > 0;
    }
};
