<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table): void {
            $table->id();
            $table->string('public_token')->unique();
            $table->string('visitor_name');
            $table->string('visitor_phone');
            $table->string('visitor_email')->nullable();
            $table->enum('status', ['bot', 'human', 'closed'])->default('bot');
            $table->boolean('ai_enabled')->default(true);
            $table->boolean('human_requested')->default(false);
            $table->unsignedInteger('admin_unread_count')->default(0);
            $table->unsignedInteger('customer_unread_count')->default(0);
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('customer_last_seen_at')->nullable();
            $table->timestamp('admin_last_seen_at')->nullable();
            $table->text('last_message_preview')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
            $table->enum('sender_type', ['customer', 'admin', 'ai', 'system']);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('message');
            $table->json('meta')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
    }
};
