<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('signature_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_request_id')
                ->constrained('document_requests')
                ->onDelete('cascade');

            $table->foreignId('authority_id')
                ->constrained('signature_authorities')
                ->onDelete('cascade');

            $table->string('token', 64)->unique();
            $table->enum('type', ['signature_upload'])->default('signature_upload');
            $table->enum('status', ['pending', 'used', 'expired'])->default('pending');

            $table->timestamp('used_at')->nullable();

            $table->timestamp('expires_at')
                ->nullable()
                ->comment('Link expire 7 hari');

            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['token', 'status']);
            $table->index(['document_request_id']);
            $table->index(['authority_id']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('signature_tokens');
    }
};
