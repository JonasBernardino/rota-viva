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
        Schema::create('eventos', function (Blueprint $table): void {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
