<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('document_requests', 'is_marked_as_taken')) {
                $table->boolean('is_marked_as_taken')->default(false);
            }

            if (!Schema::hasColumn('document_requests', 'marked_as_taken_at')) {
                $table->timestamp('marked_as_taken_at')->nullable()->after('is_marked_as_taken');
            }

            if (!Schema::hasColumn('document_requests', 'marked_as_taken_by')) {
                $table->foreignId('marked_as_taken_by')
                    ->nullable()
                    ->after('marked_as_taken_at')
                    ->constrained('users')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('document_requests', 'marked_by_role')) {
                $table->string('marked_by_role')->nullable()->after('marked_as_taken_by');
            }

            if (!Schema::hasColumn('document_requests', 'taken_notes')) {
                $table->text('taken_notes')->nullable()->after('marked_by_role');
            }
        });

        // ✅ Index untuk query performa (Laravel 12 way - tanpa Doctrine)
        if (!$this->indexExists('document_requests', 'document_requests_is_marked_as_taken_index')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->index('is_marked_as_taken');
            });
        }

        if (!$this->indexExists('document_requests', 'document_requests_delivery_method_is_marked_as_taken_index')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->index(['delivery_method', 'is_marked_as_taken']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('document_requests', 'marked_as_taken_by')) {
                $table->dropForeign(['marked_as_taken_by']);
            }
        });

        // Drop indexes
        if ($this->indexExists('document_requests', 'document_requests_is_marked_as_taken_index')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->dropIndex(['is_marked_as_taken']);
            });
        }

        if ($this->indexExists('document_requests', 'document_requests_delivery_method_is_marked_as_taken_index')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->dropIndex(['delivery_method', 'is_marked_as_taken']);
            });
        }

        Schema::table('document_requests', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('document_requests', 'is_marked_as_taken')) {
                $columnsToDrop[] = 'is_marked_as_taken';
            }
            if (Schema::hasColumn('document_requests', 'marked_as_taken_at')) {
                $columnsToDrop[] = 'marked_as_taken_at';
            }
            if (Schema::hasColumn('document_requests', 'marked_as_taken_by')) {
                $columnsToDrop[] = 'marked_as_taken_by';
            }
            if (Schema::hasColumn('document_requests', 'marked_by_role')) {
                $columnsToDrop[] = 'marked_by_role';
            }
            if (Schema::hasColumn('document_requests', 'taken_notes')) {
                $columnsToDrop[] = 'taken_notes';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
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
