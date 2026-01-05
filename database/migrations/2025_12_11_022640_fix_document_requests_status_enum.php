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
            $table->string('status_temp', 50)->nullable()->after('status');
        });

        DB::statement('UPDATE document_requests SET status_temp = status');

        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('document_requests', function (Blueprint $table) {
            $table->enum('status', [
                'submitted',
                'pending',
                'approved',

                'verification_step_1_requested',
                'verification_step_1_approved',
                'verification_step_2_requested',
                'verification_step_2_approved',
                'verification_step_3_requested',
                'verification_step_3_approved',

                'verification_requested',
                'verification_approved',
                'verification_rejected',

                'rejected',
                'processing',

                'signature_requested',
                'waiting_signature',
                'signature_in_progress',
                'signature_completed',
                'signature_verified',

                'ready_for_pickup',
                'picked_up',
                'completed'
            ])->default('submitted')->after('delivery_method');
        });

        DB::statement('UPDATE document_requests SET status = status_temp');

        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn('status_temp');
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->string('status_temp', 50)->nullable()->after('status');
        });

        DB::statement('UPDATE document_requests SET status_temp = status');

        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('document_requests', function (Blueprint $table) {
            $table->enum('status', [
                'submitted',
                'pending',
                'approved',
                'processing',
                'ready_for_pickup',
                'picked_up',
                'completed',
                'rejected'
            ])->default('submitted')->after('delivery_method');
        });

        DB::statement("
            UPDATE document_requests
            SET status = CASE
                WHEN status_temp IN ('verification_requested', 'verification_approved') THEN 'processing'
                WHEN status_temp = 'verification_rejected' THEN 'rejected'
                WHEN status_temp IN ('waiting_signature', 'signature_in_progress', 'signature_completed', 'signature_verified') THEN 'processing'
                ELSE status_temp
            END
        ");

        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn('status_temp');
        });
    }
};
