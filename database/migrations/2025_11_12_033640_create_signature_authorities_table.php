<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signature_authorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('authority_type', ['academic', 'student_affairs']);
            $table->string('position');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_authorities');
    }
};
