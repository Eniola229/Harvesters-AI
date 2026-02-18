<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('church_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('channel')->default('whatsapp'); // whatsapp, sms, web
            $table->boolean('morning_alert')->default(false);
            $table->time('alert_time')->nullable()->default('06:00:00');
            $table->string('campus')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_interaction_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('church_members');
    }
};