<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('actor_name')->nullable(); // ✅ FIXED: Added ->nullable()
            $table->string('actor_type')->nullable(); // ✅ FIXED: Added ->nullable() for guest

            // ✅ Enum yang sudah FIX
            $table->enum('activity_type', [
                'submitted',
                'approved',
                'rejected',
                'processing',
                'file_uploaded',
                'ready',              // ✅ Pakai 'ready' bukan 'ready_for_pickup'
                'picked_up',
                'downloaded',
                'completed',
                'cancelled',
                'note_added',
                'status_updated',
            ]);

            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();
            $table->text('description')->nullable(); // ✅ Pakai 'description' bukan 'notes'
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['document_request_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_activities');
    }
};
