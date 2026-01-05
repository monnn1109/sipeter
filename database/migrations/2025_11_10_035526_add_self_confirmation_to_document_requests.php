<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->timestamp('downloaded_at')->nullable()->after('picked_up_at');
            $table->timestamp('self_confirmed_at')->nullable()->after('downloaded_at');
            $table->string('confirmed_by')->nullable()->after('self_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn(['downloaded_at', 'self_confirmed_at', 'confirmed_by']);
        });
    }
};
