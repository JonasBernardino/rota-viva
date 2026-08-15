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
        Schema::create('interacoes', function (Blueprint $table): void {
            $table->id();
            $table->string('sessao_id')->index();
            $table->string('tipo_interacao')->index()->comment('view_place, generate_route, adapt_rain, click_business, download_itinerary');
            $table->string('entidade_tipo')->nullable();
            $table->unsignedBigInteger('entidade_id')->nullable();
            $table->json('metadados')->nullable();
            $table->string('ip_anonimizado', 64)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('criado_em')->useCurrent();
        });

        Schema::create('insights_destino', function (Blueprint $table): void {
            $table->id();
            $table->date('data_referencia')->index();
            $table->string('metrica')->index();
            $table->decimal('valor', 14, 4);
            $table->json('dimensoes')->nullable();
            $table->timestamps();
        });

        Schema::create('logs_auditoria', function (Blueprint $table): void {
            $table->id();
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_auditoria');
        Schema::dropIfExists('insights_destino');
        Schema::dropIfExists('interacoes');
    }
};
