<?php

namespace App\Services\Adaptation;

use App\Contracts\PlaceRepository;
use App\DTOs\AdaptedItineraryDTO;
use App\DTOs\ItineraryStopDTO;
use App\Models\Itinerary;
use App\Models\Place;
use RuntimeException;

class RouteAdaptationService
{
    public function __construct(
        private readonly PlaceRepository $places,
    ) {}

    public function adaptForRain(
        Itinerary $itinerary
    ): AdaptedItineraryDTO {
        $itinerary->load([
            'items.place.category',
            'items.place.accessibilityFeatures',
            'preference',
        ]);

        $removed = [];
        $kept = [];

        foreach ($itinerary->items as $item) {
            $place = $item->place;

            if ($place->is_outdoor) {
                $removed[] = $place->id;

                continue;
            }

            $kept[] = new ItineraryStopDTO(
                placeId: $place->id,
                name: $place->name,
                category: $place->category?->name
                    ?? 'Experiência',
                durationMinutes: $item->duration_minutes,
                estimatedCost: $item->estimated_cost,
                latitude: $place->latitude,
                longitude: $place->longitude,
                isOutdoor: $place->is_outdoor,
                score: 0,
            );
        }

        if (empty($removed)) {
            return new AdaptedItineraryDTO(
                stops: $kept,
                removedPlaceIds: [],
                addedPlaceIds: [],
                totalDurationMinutes: collect($kept)
                    ->sum('durationMinutes'),
                totalEstimatedCost: collect($kept)
                    ->sum('estimatedCost'),
            );
        }

        $replacementCandidates =
            $this->findReplacementCandidates(
                itinerary: $itinerary,
                excludedIds: array_merge(
                    $removed,
                    collect($kept)
                        ->pluck('placeId')
                        ->all()
                ),
            );

        $added = [];

        foreach ($removed as $removedPlaceId) {
            $replacement =
                $replacementCandidates
                    ->shift();

            if (! $replacement) {
                continue;
            }

            $stop = new ItineraryStopDTO(
                placeId: $replacement->id,
                name: $replacement->name,
                category: $replacement->category?->name
                    ?? 'Experiência',
                durationMinutes: $replacement->duration_minutes,
                estimatedCost: $replacement->average_cost,
                latitude: $replacement->latitude,
                longitude: $replacement->longitude,
                isOutdoor: $replacement->is_outdoor,
                score: 0,
            );

            $kept[] = $stop;

            $added[] = $replacement->id;
        }

        $ordered = $this->orderStops(
            $itinerary,
            $kept
        );

        $duration =
            collect($ordered)
                ->sum('durationMinutes');

        $cost =
            collect($ordered)
                ->sum('estimatedCost');

        if (
            $itinerary->preference->available_minutes
            !== null
            && $duration
                > $itinerary
                    ->preference
                    ->available_minutes
        ) {
            throw new RuntimeException(
                'A rota adaptada ultrapassa o tempo disponível.'
            );
        }

        if (
            $itinerary->preference->budget
            !== null
            && $cost
                > $itinerary
                    ->preference
                    ->budget
        ) {
            throw new RuntimeException(
                'A rota adaptada ultrapassa o orçamento.'
            );
        }

        return new AdaptedItineraryDTO(
            stops: $ordered,
            removedPlaceIds: $removed,
            addedPlaceIds: $added,
            totalDurationMinutes: $duration,
            totalEstimatedCost: $cost,
        );
    }

    private function findReplacementCandidates(
        Itinerary $itinerary,
        array $excludedIds,
    ) {
        $preferences =
            $itinerary->preference;

        return $this->places
            ->getAvailablePlaces()
            ->filter(
                fn (Place $place) => ! $place->is_outdoor
            )
            ->reject(
                fn (Place $place) => in_array(
                    $place->id,
                    $excludedIds,
                    true
                )
            )
            ->filter(function (Place $place) use (
                $preferences
            ) {
                if (
                    $preferences->has_children
                    && ! $place->suitable_for_children
                ) {
                    return false;
                }

                if (
                    $preferences->budget !== null
                    && $place->average_cost
                        > $preferences->budget
                ) {
                    return false;
                }

                return true;
            })
            ->sortByDesc(
                fn (Place $place) => $this->calculateCompatibility(
                    $place,
                    $preferences
                )
            )
            ->values();
    }

    private function calculateCompatibility(
        Place $place,
        $preferences
    ): int {
        $score = 0;

        $tags = collect(
            $place->tags ?? []
        )->map(
            fn ($tag) => mb_strtolower($tag)
        );

        foreach (
            $preferences->interests ?? [] as $interest
        ) {
            if (
                $tags->contains(
                    mb_strtolower($interest)
                )
            ) {
                $score += 25;
            }
        }

        foreach (
            $preferences->moods ?? [] as $mood
        ) {
            if (
                $tags->contains(
                    mb_strtolower($mood)
                )
            ) {
                $score += 15;
            }
        }

        if (
            $preferences->intensity
            && $place->intensity
                === $preferences->intensity
        ) {
            $score += 10;
        }

        if (
            $preferences->has_children
            && $place->suitable_for_children
        ) {
            $score += 10;
        }

        return $score;
    }

    private function orderStops(
        Itinerary $itinerary,
        array $stops
    ): array {
        $originalOrder =
            $itinerary->items
                ->pluck(
                    'position',
                    'place_id'
                );

        usort(
            $stops,
            function (
                ItineraryStopDTO $a,
                ItineraryStopDTO $b
            ) use ($originalOrder) {
                $positionA =
                    $originalOrder[$a->placeId]
                    ?? PHP_INT_MAX;

                $positionB =
                    $originalOrder[$b->placeId]
                    ?? PHP_INT_MAX;

                return $positionA <=> $positionB;
            }
        );

        return $stops;
    }
}
