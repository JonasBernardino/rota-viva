<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('journey_events')) {
            Schema::create('journey_events', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
                $table->uuid('session_uuid');
                $table->string('event_type', 50);
                $table->jsonb('payload');
                $table->timestampTz('occurred_at');
                $table->timestampsTz();

                $table->index(['municipio_id', 'event_type']);
                $table->index(['municipio_id', 'session_uuid']);
                $table->index(['municipio_id', 'occurred_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journey_events');
    }
};
