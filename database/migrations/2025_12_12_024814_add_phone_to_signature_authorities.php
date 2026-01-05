<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signature_authorities', function (Blueprint $table) {
            if (!Schema::hasColumn('signature_authorities', 'phone')) {
                $table->string('phone', 20)->nullable()->after('position');
            }
        });
    }

    public function down(): void
    {
        Schema::table('signature_authorities', function (Blueprint $table) {
            if (Schema::hasColumn('signature_authorities', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
