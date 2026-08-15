<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journey_events', function (Blueprint $table) {
            $table->id();

            /*
             * UUID anônimo da jornada/sessão.
             *
             * Não é usuário, CPF, e-mail etc.
             */
            $table->uuid('session_uuid');

            $table->string('event_type', 50);

            /*
             * Dados específicos do evento.
             *
             * PostgreSQL JSONB nos dá flexibilidade
             * sem criar dezenas de colunas analíticas.
             */
            $table->jsonb('payload');

            $table->timestampTz('occurred_at');

            $table->timestampsTz();

            $table->index('event_type');
            $table->index('session_uuid');
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journey_events');
    }
};