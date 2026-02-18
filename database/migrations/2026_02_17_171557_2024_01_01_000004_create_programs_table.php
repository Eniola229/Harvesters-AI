<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->string('image_public_id')->nullable(); // cloudinary public id
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('venue')->nullable();
            $table->string('campus')->nullable(); // all or specific campus
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('metadata')->nullable(); // bus locations, free meal, dress code, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};