<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            UPDATE document_requests
            SET status = 'ready_for_pickup'
            WHERE status = '' OR status IS NULL
        ");

        $updated = DB::table('document_requests')
            ->where('status', 'ready_for_pickup')
            ->count();

        echo "✅ Fixed {$updated} documents with empty status\n";
    }

    public function down(): void
    {
        DB::statement("
            UPDATE document_requests
            SET status = ''
            WHERE status = 'ready_for_pickup'
            AND file_uploaded_at IS NOT NULL
            AND approved_at IS NOT NULL
        ");
    }
};
