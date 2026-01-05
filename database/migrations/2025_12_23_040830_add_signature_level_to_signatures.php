<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('document_signatures', 'signature_level')) {
            Schema::table('document_signatures', function (Blueprint $table) {
                $table->tinyInteger('signature_level')->default(1)->after('signature_authority_id');
                $table->index('signature_level');
            });
        }

        if (!Schema::hasColumn('document_requests', 'current_signature_step')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->tinyInteger('current_signature_step')->default(0)->after('current_verification_step');
                $table->index('current_signature_step');
            });
        }
    }

    public function down(): void
    {
        Schema::table('document_signatures', function (Blueprint $table) {
            $table->dropColumn('signature_level');
        });

        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn('current_signature_step');
        });
    }
};
