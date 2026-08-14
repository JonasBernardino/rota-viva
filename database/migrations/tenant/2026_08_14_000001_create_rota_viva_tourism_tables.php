<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create(
            'accessibility_features',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            }
        );

        Schema::create('places', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->unsignedInteger(
                'duration_minutes'
            );

            $table
                ->decimal('average_cost', 10, 2)
                ->default(0);

            $table
                ->boolean('is_outdoor')
                ->default(false);

            $table
                ->boolean('suitable_for_children')
                ->default(true);

            $table
                ->string('intensity')
                ->default('low');

            $table->decimal(
                'latitude',
                10,
                7
            );

            $table->decimal(
                'longitude',
                10,
                7
            );

            $table->json('tags')->nullable();

            $table
                ->boolean('is_available')
                ->default(true);

            $table->timestamps();
        });

        Schema::create(
            'place_schedules',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('place_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->unsignedTinyInteger(
                    'day_of_week'
                );

                $table->time('opens_at');
                $table->time('closes_at');

                $table->timestamps();
            }
        );

        Schema::create(
            'place_accessibility_features',
            function (Blueprint $table) {
                $table
                    ->foreignId('place_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table
                    ->foreignId(
                        'accessibility_feature_id'
                    )
                    ->constrained()
                    ->cascadeOnDelete();

                $table->primary([
                    'place_id',
                    'accessibility_feature_id',
                ]);
            }
        );

        Schema::create(
            'visitor_preferences',
            function (Blueprint $table) {
                $table->id();

                $table->text(
                    'original_description'
                );

                $table->json('moods');
                $table->json('interests');

                $table
                    ->unsignedInteger(
                        'available_minutes'
                    )
                    ->nullable();

                $table
                    ->decimal('budget', 10, 2)
                    ->nullable();

                $table
                    ->boolean('has_children')
                    ->nullable();

                $table
                    ->string('transport')
                    ->nullable();

                $table->json(
                    'accessibility_requirements'
                );

                $table
                    ->string('intensity')
                    ->nullable();

                $table->timestamps();
            }
        );

        Schema::create(
            'itineraries',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId(
                        'visitor_preference_id'
                    )
                    ->constrained()
                    ->cascadeOnDelete();

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

                $table
                    ->string('status')
                    ->default('ACTIVE');

                $table->timestamps();
            }
        );

        Schema::create(
            'itinerary_items',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('itinerary_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table
                    ->foreignId('place_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->unsignedInteger('position');

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

                $table->unique([
                    'itinerary_id',
                    'position',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'itinerary_items'
        );

        Schema::dropIfExists(
            'itineraries'
        );

        Schema::dropIfExists(
            'visitor_preferences'
        );

        Schema::dropIfExists(
            'place_accessibility_features'
        );

        Schema::dropIfExists(
            'place_schedules'
        );

        Schema::dropIfExists('places');

        Schema::dropIfExists(
            'accessibility_features'
        );

        Schema::dropIfExists('categories');
    }
};