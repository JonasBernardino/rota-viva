<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_adaptations', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('itinerary_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('event');

            $table->string('title');
            $table->text('summary');

            $table->unsignedInteger(
                'total_duration_minutes'
            );

            $table->decimal(
                'total_estimated_cost',
                10,
                2
            );

            $table->timestamps();
        });

        Schema::create(
            'route_adaptation_items',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('route_adaptation_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table
                    ->foreignId('place_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->unsignedInteger('position');

                $table->string('action');

                $table->unsignedInteger(
                    'duration_minutes'
                );

                $table->decimal(
                    'estimated_cost',
                    10,
                    2
                );

                $table->text('reason');

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'route_adaptation_items'
        );

        Schema::dropIfExists(
            'route_adaptations'
        );
    }
};