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
        Schema::create('midias_atrativos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('atrativo_id')->constrained('atrativos')->cascadeOnDelete();
            $table->string('tipo')->default('image'); // image, video, virtual_tour
            $table->string('url');
            $table->string('titulo')->nullable();
            $table->text('descricao_acessibilidade')->nullable();
            $table->string('autor')->nullable();
            $table->boolean('is_destaque')->default(false);
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('midias_atrativos');
    }
};
