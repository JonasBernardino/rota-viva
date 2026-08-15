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
        Schema::create('municipios', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->string('codigo_ibge', 10)->nullable()->unique();
            $table->string('uf', 2)->default('PB');
            $table->string('nome_schema')->unique();
            $table->string('status')->default('active')->index()->comment('active, inactive, suspended');
            $table->string('fuso_horario')->default('America/Fortaleza');
            $table->json('configuracoes')->nullable();
            $table->timestamps();
        });

        Schema::create('dominios_municipios', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
            $table->string('dominio')->unique();
            $table->boolean('is_principal')->default(false);
            $table->timestamp('verificado_em')->nullable();
            $table->timestamps();
        });

        Schema::create('usuarios_plataforma', function (Blueprint $table): void {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('senha');
            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->nullOnDelete();
            $table->string('papel')->default('gestor_municipal')->comment('super_admin, gestor_municipal, auditor');
            $table->boolean('is_ativo')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_plataforma');
        Schema::dropIfExists('dominios_municipios');
        Schema::dropIfExists('municipios');
    }
};
