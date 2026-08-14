<?php

namespace App\Services\Itinerary;

use App\DTOs\ExperienceNarrativeDTO;
use App\DTOs\GeneratedItineraryDTO;
use App\DTOs\VisitorPreferencesDTO;
use App\Models\Itinerary;
use App\Models\VisitorPreference;
use Illuminate\Support\Facades\DB;

class ItineraryPersistenceService
{
    public function store(
        string $description,
        VisitorPreferencesDTO $preferences,
        GeneratedItineraryDTO $generated,
        ExperienceNarrativeDTO $narrative
    ): Itinerary {
        return DB::transaction(function () use (
            $description,
            $preferences,
            $generated,
            $narrative
        ) {
            $preference = VisitorPreference::create([
                'original_description' => $description,
                'moods' => $preferences->moods,
                'interests' => $preferences->interests,
                'available_minutes' =>
                $preferences->availableMinutes,
                'budget' => $preferences->budget,
                'has_children' =>
                $preferences->hasChildren,
                'transport' => $preferences->transport,
                'accessibility_requirements' =>
                $preferences
                    ->accessibilityRequirements,
                'intensity' =>
                $preferences->intensity,
            ]);

            $itinerary = Itinerary::create([
                'visitor_preference_id' =>
                $preference->id,

                'title' => $narrative->title,
                'summary' => $narrative->summary,

                'total_duration_minutes' =>
                $generated->totalDurationMinutes,

                'total_estimated_cost' =>
                $generated->totalEstimatedCost,

                'status' => 'ACTIVE',
            ]);

            foreach (
                $generated->stops
                as $position => $stop
            ) {
                $itinerary->items()->create([
                    'place_id' => $stop->placeId,
                    'position' => $position + 1,
                    'duration_minutes' =>
                    $stop->durationMinutes,

                    'estimated_cost' =>
                    $stop->estimatedCost,

                    'reason' =>
                    $narrative
                        ->reasonFor($stop->placeId),
                ]);
            }

            return $itinerary;
        });
    }
}
