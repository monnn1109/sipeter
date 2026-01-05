<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name')->unique();
            $table->string('code_prefix', 10)->unique();
            $table->text('description')->nullable();

            // Applicability & Processing
            $table->json('applicable_for'); // ['mahasiswa'] atau ['dosen', 'staff']
            $table->integer('processing_days')->default(3); // ✅ NAMA KONSISTEN dengan Seeder
            $table->json('required_fields')->nullable(); // ✅ TAMBAH nullable

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('is_active');
            $table->index('code_prefix');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_types');
    }
};
