<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('member_id')->nullable();
            $table->foreign('member_id')->references('id')->on('church_members')->onDelete('set null');
            $table->string('phone');
            $table->string('channel')->default('whatsapp');
            $table->json('context')->nullable(); // stores last N messages for context
            $table->string('state')->default('active'); // active, waiting_name
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('conversation_id');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->string('role'); // user, assistant
            $table->text('content');
            $table->string('media_url')->nullable();
            $table->string('media_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};