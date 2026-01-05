<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\UserRole; // Pastikan use statement ini ada

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();

        // ==========================================================
        // TAMBAHAN DARI SEEDER ANDA (TERMASUK PENYEBAB ERROR)
        // ==========================================================
        $table->string('nip_nidn')->unique()->nullable();
        $table->string('phone_number')->nullable();
        $table->string('role'); // dari UserRole Enum
        $table->boolean('is_active')->default(true); // <-- INI PENYEBAB ERROR
        // ==========================================================

        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
