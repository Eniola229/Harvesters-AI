<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Campuses
        Schema::create('campuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('Nigeria');
            $table->string('pastor_name')->nullable();
            $table->string('pastor_phone')->nullable();
            $table->string('service_times')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Leaders
        Schema::create('leaders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('title')->nullable(); // Senior Pastor, Associate Pastor, etc.
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('bio')->nullable();
            $table->string('image_url')->nullable();
            $table->uuid('campus_id')->nullable();
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('set null');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Church info / FAQs / general knowledge
        Schema::create('church_infos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category'); // about, values, faq, services, giving, contact
            $table->string('title');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Newsletters
        Schema::create('newsletters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('message');
            $table->string('media_url')->nullable();
            $table->string('media_type')->nullable(); // image, video
            $table->string('media_public_id')->nullable();
            $table->string('target_campus')->default('all');
            $table->integer('sent_count')->default(0);
            $table->string('status')->default('draft'); // draft, sending, sent
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletters');
        Schema::dropIfExists('church_infos');
        Schema::dropIfExists('leaders');
        Schema::dropIfExists('campuses');
    }
};