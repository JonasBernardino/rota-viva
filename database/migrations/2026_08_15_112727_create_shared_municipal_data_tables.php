<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categorias')) {
            Schema::create('categorias', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->string('nome');
                $table->string('slug');
                $table->timestamps();
                $table->unique(['municipio_id', 'slug']);
            });
        }

        if (! Schema::hasTable('recursos_acessibilidade')) {
            Schema::create('recursos_acessibilidade', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->string('nome');
                $table->string('slug');
                $table->timestamps();
                $table->unique(['municipio_id', 'slug']);
            });
        }

        if (! Schema::hasTable('atrativos')) {
            Schema::create('atrativos', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
                $table->string('nome');
                $table->string('slug');
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
                $table->unique(['municipio_id', 'slug']);
                $table->index(['municipio_id', 'is_disponivel']);
            });
        }

        if (! Schema::hasTable('horarios_atrativos')) {
            Schema::create('horarios_atrativos', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->foreignId('atrativo_id')->constrained('atrativos')->cascadeOnDelete();
                $table->unsignedTinyInteger('dia_semana');
                $table->time('abre_as');
                $table->time('fecha_as');
                $table->timestamps();
                $table->unique(['atrativo_id', 'dia_semana']);
            });
        }

        if (! Schema::hasTable('atrativo_recursos_acessibilidade')) {
            Schema::create('atrativo_recursos_acessibilidade', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->foreignId('atrativo_id')->constrained('atrativos')->cascadeOnDelete();
                $table->foreignId('recurso_acessibilidade_id')->constrained('recursos_acessibilidade')->cascadeOnDelete();
                $table->unique(['municipio_id', 'atrativo_id', 'recurso_acessibilidade_id'], 'atrativo_recurso_municipio_unique');
            });
        }

        if (! Schema::hasTable('preferencias_visitantes')) {
            Schema::create('preferencias_visitantes', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
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
        }

        if (! Schema::hasTable('roteiros')) {
            Schema::create('roteiros', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->foreignId('preferencia_visitante_id')->constrained('preferencias_visitantes')->cascadeOnDelete();
                $table->string('titulo');
                $table->text('resumo');
                $table->unsignedInteger('duracao_total_minutos');
                $table->decimal('custo_total_estimado', 10, 2);
                $table->string('status')->default('ACTIVE');
                $table->timestamps();
                $table->index(['municipio_id', 'status']);
            });
        }

        if (! Schema::hasTable('itens_roteiro')) {
            Schema::create('itens_roteiro', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->foreignId('roteiro_id')->constrained('roteiros')->cascadeOnDelete();
                $table->foreignId('atrativo_id')->constrained('atrativos')->cascadeOnDelete();
                $table->unsignedInteger('posicao');
                $table->unsignedInteger('duracao_minutos');
                $table->decimal('custo_estimado', 10, 2);
                $table->text('motivo');
                $table->timestamps();
                $table->unique(['roteiro_id', 'posicao']);
            });
        }

        if (! Schema::hasTable('adaptacoes_rota')) {
            Schema::create('adaptacoes_rota', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->foreignId('roteiro_id')->constrained('roteiros')->cascadeOnDelete();
                $table->string('evento');
                $table->string('titulo');
                $table->text('resumo');
                $table->unsignedInteger('duracao_total_minutos');
                $table->decimal('custo_total_estimado', 10, 2);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('itens_adaptacao_rota')) {
            Schema::create('itens_adaptacao_rota', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->foreignId('adaptacao_rota_id')->constrained('adaptacoes_rota')->cascadeOnDelete();
                $table->foreignId('atrativo_id')->constrained('atrativos')->cascadeOnDelete();
                $table->unsignedInteger('posicao');
                $table->string('acao');
                $table->unsignedInteger('duracao_minutos');
                $table->decimal('custo_estimado', 10, 2);
                $table->text('motivo');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('midias_atrativos')) {
            Schema::create('midias_atrativos', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->foreignId('atrativo_id')->constrained('atrativos')->cascadeOnDelete();
                $table->string('tipo')->default('image');
                $table->string('url');
                $table->string('titulo')->nullable();
                $table->text('descricao_acessibilidade')->nullable();
                $table->string('autor')->nullable();
                $table->boolean('is_destaque')->default(false);
                $table->unsignedInteger('ordem')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('estabelecimentos')) {
            Schema::create('estabelecimentos', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->string('nome');
                $table->string('slug');
                $table->string('tipo_estabelecimento')->index();
                $table->text('descricao');
                $table->string('endereco')->nullable();
                $table->string('bairro')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->string('telefone')->nullable();
                $table->string('whatsapp')->nullable();
                $table->string('instagram')->nullable();
                $table->string('website')->nullable();
                $table->string('faixa_preco', 10)->default('$$');
                $table->boolean('tem_selo_qualidade')->default(false)->index();
                $table->string('status_validacao')->default('approved')->index();
                $table->text('notas_validacao')->nullable();
                $table->timestamp('validado_em')->nullable();
                $table->string('imagem_capa')->nullable();
                $table->timestamps();
                $table->unique(['municipio_id', 'slug']);
            });
        }

        if (! Schema::hasTable('eventos')) {
            Schema::create('eventos', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->string('nome');
                $table->string('slug');
                $table->text('descricao');
                $table->string('nome_local');
                $table->string('endereco')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->dateTime('inicia_em')->index();
                $table->dateTime('termina_em')->nullable();
                $table->boolean('is_gratuito')->default(true);
                $table->decimal('preco', 10, 2)->nullable();
                $table->boolean('is_acessivel')->default(true);
                $table->string('categoria')->default('cultural');
                $table->string('organizador')->nullable();
                $table->integer('capacidade')->nullable();
                $table->string('status')->default('scheduled');
                $table->string('imagem_url')->nullable();
                $table->timestamps();
                $table->unique(['municipio_id', 'slug']);
            });
        }

        if (! Schema::hasTable('interacoes')) {
            Schema::create('interacoes', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->string('sessao_id')->index();
                $table->string('tipo_interacao')->index();
                $table->string('entidade_tipo')->nullable();
                $table->unsignedBigInteger('entidade_id')->nullable();
                $table->json('metadados')->nullable();
                $table->string('ip_anonimizado', 64)->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamp('criado_em')->useCurrent();
            });
        }

        if (! Schema::hasTable('insights_destino')) {
            Schema::create('insights_destino', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->date('data_referencia')->index();
                $table->string('metrica')->index();
                $table->decimal('valor', 14, 4);
                $table->json('dimensoes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('logs_auditoria')) {
            Schema::create('logs_auditoria', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('acao')->index();
                $table->string('entidade_tipo');
                $table->unsignedBigInteger('entidade_id')->nullable();
                $table->json('valores_anteriores')->nullable();
                $table->json('valores_novos')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('criado_em')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_auditoria');
        Schema::dropIfExists('insights_destino');
        Schema::dropIfExists('interacoes');
        Schema::dropIfExists('eventos');
        Schema::dropIfExists('estabelecimentos');
        Schema::dropIfExists('midias_atrativos');
        Schema::dropIfExists('itens_adaptacao_rota');
        Schema::dropIfExists('adaptacoes_rota');
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
