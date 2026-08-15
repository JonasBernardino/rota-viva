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
        Schema::create('businesses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('business_type')->index()->comment('gastronomy, lodging, tour_guide, craft, agency, transport');
            $table->text('description');
            $table->string('address')->nullable();
            $table->string('neighborhood')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('instagram')->nullable();
            $table->string('website')->nullable();
            $table->string('price_range', 10)->default('$$');
            $table->boolean('has_seal_of_quality')->default(false)->index();
            $table->string('validation_status')->default('approved')->index()->comment('pending, approved, rejected, suspended');
            $table->text('validation_notes')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
