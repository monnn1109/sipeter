<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Null = broadcast
            $table->string('type'); // document_request, document_status, etc
            $table->string('title');
            $table->text('message');
            $table->foreignId('document_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('action_url')->nullable();
            $table->string('icon')->default('bell');
            $table->string('color')->default('blue');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_notifications');
    }
};
