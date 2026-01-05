<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('document_verifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_request_id')
                  ->constrained('document_requests')
                  ->onDelete('cascade')
                  ->comment('Reference ke dokumen yang diverifikasi');

            $table->foreignId('authority_id')
                  ->constrained('signature_authorities')
                  ->onDelete('cascade')
                  ->comment('Pejabat yang melakukan verifikasi');

            $table->string('token', 64)->unique()->comment('Token unik untuk link verifikasi');

            $table->enum('type', ['document_verification'])
                  ->default('document_verification')
                  ->comment('Tipe verifikasi');

            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->comment('Status verifikasi');

            $table->enum('decision', ['approved', 'rejected'])
                  ->nullable()
                  ->comment('Keputusan final pejabat');

            $table->text('notes')
                  ->nullable()
                  ->comment('Catatan/alasan jika ditolak');

            $table->timestamp('verified_at')
                  ->nullable()
                  ->comment('Waktu pejabat melakukan verifikasi');

            $table->timestamp('expires_at')
                  ->nullable()
                  ->comment('Link verifikasi expire 3 hari dari sent_at');

            $table->timestamp('sent_at')
                  ->nullable()
                  ->comment('Waktu link verifikasi dikirim');

            $table->timestamps();

            $table->index('token');
            $table->index('status');
            $table->index(['document_request_id', 'status']);
            $table->index('expires_at');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('document_verifications');
    }
};
