<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE document_requests MODIFY COLUMN status VARCHAR(100)");

        DB::statement("
            ALTER TABLE document_requests
            MODIFY COLUMN status ENUM(
                -- Initial States
                'submitted',
                'pending',
                'approved',

                -- 3-Level Verification (Sequential)
                'verification_step_1_requested',
                'verification_step_1_approved',
                'verification_step_2_requested',
                'verification_step_2_approved',
                'verification_step_3_requested',
                'verification_step_3_approved',
                'verification_rejected',

                'rejected',
                'processing',

                -- 3-Level Signature (TTD Sequential)
                'signature_requested',
                'signature_uploaded',
                'signature_level_1_requested',
                'signature_level_1_uploaded',
                'signature_level_2_requested',
                'signature_level_2_uploaded',
                'signature_level_3_requested',
                'signature_level_3_uploaded',
                'signature_verified',
                'signature_completed',

                -- Legacy (backward compatibility)
                'waiting_signature',
                'signature_in_progress',

                -- Final States
                'ready_for_pickup',
                'picked_up',
                'completed'
            ) DEFAULT 'submitted'
        ");

        DB::statement("ALTER TABLE document_requests MODIFY COLUMN status ENUM(
            'submitted', 'pending', 'approved',
            'verification_step_1_requested', 'verification_step_1_approved',
            'verification_step_2_requested', 'verification_step_2_approved',
            'verification_step_3_requested', 'verification_step_3_approved',
            'verification_rejected', 'rejected', 'processing',
            'signature_requested', 'signature_uploaded',
            'signature_level_1_requested', 'signature_level_1_uploaded',
            'signature_level_2_requested', 'signature_level_2_uploaded',
            'signature_level_3_requested', 'signature_level_3_uploaded',
            'signature_verified', 'signature_completed',
            'waiting_signature', 'signature_in_progress',
            'ready_for_pickup', 'picked_up', 'completed'
        ) DEFAULT 'submitted'");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE document_requests
            SET status = 'processing'
            WHERE status NOT IN (
                'submitted', 'pending', 'approved', 'processing',
                'ready_for_pickup', 'picked_up', 'completed', 'rejected'
            )
        ");

        DB::statement("ALTER TABLE document_requests MODIFY COLUMN status VARCHAR(100)");

        DB::statement("
            ALTER TABLE document_requests
            MODIFY COLUMN status ENUM(
                'submitted',
                'pending',
                'approved',
                'processing',
                'ready_for_pickup',
                'picked_up',
                'completed',
                'rejected'
            ) DEFAULT 'submitted'
        ");
    }
};
