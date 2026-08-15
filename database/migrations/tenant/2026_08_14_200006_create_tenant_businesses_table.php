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
        Schema::create('estabelecimentos', function (Blueprint $table): void {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->string('tipo_estabelecimento')->index()->comment('gastronomia, hospedagem, guia_turistico, artesanato, agencia, transporte, atividade');
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
            $table->string('status_validacao')->default('approved')->index()->comment('pending, approved, rejected, suspended');
            $table->text('notas_validacao')->nullable();
            $table->timestamp('validado_em')->nullable();
            $table->string('imagem_capa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estabelecimentos');
    }
};
