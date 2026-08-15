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
        Schema::create('adaptacoes_rota', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('roteiro_id')->constrained('roteiros')->cascadeOnDelete();
            $table->string('evento'); // Ex: RAIN_STARTED
            $table->string('titulo');
            $table->text('resumo');
            $table->unsignedInteger('duracao_total_minutos');
            $table->decimal('custo_total_estimado', 10, 2);
            $table->timestamps();
        });

        Schema::create('itens_adaptacao_rota', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('adaptacao_rota_id')->constrained('adaptacoes_rota')->cascadeOnDelete();
            $table->foreignId('atrativo_id')->constrained('atrativos')->cascadeOnDelete();
            $table->unsignedInteger('posicao');
            $table->string('acao'); // ADDED, REMOVED, KEPT
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
        Schema::dropIfExists('itens_adaptacao_rota');
        Schema::dropIfExists('adaptacoes_rota');
    }
};
