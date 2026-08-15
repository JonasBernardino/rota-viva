<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('place_media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
            $table->string('media_type')->default('image')->comment('image, video, audio, panorama_360, drone');
            $table->string('url');
            $table->string('thumbnail_url')->nullable();
            $table->string('caption')->nullable();
            $table->string('author')->nullable();
            $table->string('license_info')->nullable();
            $table->boolean('is_cover')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('place_media');
    }
};
