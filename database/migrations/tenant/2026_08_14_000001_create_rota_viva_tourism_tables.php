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
        Schema::create('categorias', function (Blueprint $table): void {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('recursos_acessibilidade', function (Blueprint $table): void {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('atrativos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->unsignedInteger('duracao_minutos');
            $table->decimal('custo_medio', 10, 2)->default(0);
            $table->boolean('is_ar_livre')->default(false);
            $table->boolean('adequado_criancas')->default(true);
            $table->string('intensidade')->default('low');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->json('tags')->nullable();
            $table->boolean('is_disponivel')->default(true);
            $table->timestamps();
        });

        Schema::create('horarios_atrativos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('atrativo_id')->constrained('atrativos')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia_semana'); // 0 = Domingo, 1 = Segunda, ..., 6 = Sábado
            $table->time('abre_as');
            $table->time('fecha_as');
            $table->timestamps();
        });

        Schema::create('atrativo_recursos_acessibilidade', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('atrativo_id')->constrained('atrativos')->cascadeOnDelete();
            $table->foreignId('recurso_acessibilidade_id')->constrained('recursos_acessibilidade')->cascadeOnDelete();
            $table->unique(['atrativo_id', 'recurso_acessibilidade_id']);
        });

        Schema::create('preferencias_visitantes', function (Blueprint $table): void {
            $table->id();
            $table->text('descricao_original');
            $table->json('humores');
            $table->json('interesses');
            $table->unsignedInteger('minutos_disponiveis')->nullable();
            $table->decimal('orcamento', 10, 2)->nullable();
            $table->boolean('tem_criancas')->nullable();
            $table->string('transporte')->nullable();
            $table->json('requisitos_acessibilidade');
            $table->string('intensidade')->nullable();
            $table->timestamps();
        });

        Schema::create('roteiros', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('preferencia_visitante_id')->constrained('preferencias_visitantes')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('resumo');
            $table->unsignedInteger('duracao_total_minutos');
            $table->decimal('custo_total_estimado', 10, 2);
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('itens_roteiro', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('roteiro_id')->constrained('roteiros')->cascadeOnDelete();
            $table->foreignId('atrativo_id')->constrained('atrativos')->cascadeOnDelete();
            $table->unsignedInteger('posicao');
            $table->unsignedInteger('duracao_minutos');
            $table->decimal('custo_estimado', 10, 2);
            $table->text('motivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itens_roteiro');
        Schema::dropIfExists('roteiros');
        Schema::dropIfExists('preferencias_visitantes');
        Schema::dropIfExists('atrativo_recursos_acessibilidade');
        Schema::dropIfExists('horarios_atrativos');
        Schema::dropIfExists('atrativos');
        Schema::dropIfExists('recursos_acessibilidade');
        Schema::dropIfExists('categorias');
    }
};
