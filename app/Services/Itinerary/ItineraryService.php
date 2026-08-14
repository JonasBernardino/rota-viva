<?php

namespace App\Services\Itinerary;

use App\Contracts\PlaceRepository;
use App\DTOs\GeneratedItineraryDTO;
use App\DTOs\ItineraryStopDTO;
use App\DTOs\VisitorPreferencesDTO;
use App\Models\Place;
use Carbon\Carbon;
use RuntimeException;

class ItineraryService
{
    public function __construct(
        private readonly PlaceRepository $places,
    ) {
    }

    public function generate(
        VisitorPreferencesDTO $preferences
    ): GeneratedItineraryDTO {
        $places = $this->places->getAvailablePlaces();

        $candidates = $places
            ->filter(
                fn (Place $place) =>
                    $this->passesRequiredFilters(
                        $place,
                        $preferences
                    )
            )
            ->map(fn (Place $place) => [
                'place' => $place,
                'score' => $this->calculateScore(
                    $place,
                    $preferences
                ),
            ])
            ->sortByDesc('score')
            ->values();

        $selected = [];

        $totalDuration = 0;
        $totalCost = 0.0;

        $maximumDuration =
            $preferences->availableMinutes ?? 480;

        $maximumBudget =
            $preferences->budget;

        $currentTime = Carbon::now();

        foreach ($candidates as $candidate) {
            /** @var Place $place */
            $place = $candidate['place'];

            $newDuration =
                $totalDuration + $place->duration_minutes;

            $newCost =
                $totalCost + $place->average_cost;

            if ($newDuration > $maximumDuration) {
                continue;
            }

            if (
                $maximumBudget !== null
                && $newCost > $maximumBudget
            ) {
                continue;
            }

            $expectedStart = $currentTime
                ->copy()
                ->addMinutes($totalDuration);

            if (
                !$place->isOpenDuring(
                    $expectedStart,
                    $place->duration_minutes
                )
            ) {
                continue;
            }

            $selected[] = new ItineraryStopDTO(
                placeId: $place->id,
                name: $place->name,
                category: $place->category?->name ?? 'Experiência',
                durationMinutes: $place->duration_minutes,
                estimatedCost: $place->average_cost,
                latitude: $place->latitude,
                longitude: $place->longitude,
                isOutdoor: $place->is_outdoor,
                score: $candidate['score'],
            );

            $totalDuration = $newDuration;
            $totalCost = $newCost;

            if (count($selected) >= 5) {
                break;
            }
        }

        if (empty($selected)) {
            throw new RuntimeException(
                'Não encontramos uma rota compatível com as preferências informadas.'
            );
        }

        $selected = $this->orderByProximity($selected);

        return new GeneratedItineraryDTO(
            stops: $selected,
            totalDurationMinutes: $totalDuration,
            totalEstimatedCost: $totalCost,
        );
    }

    private function passesRequiredFilters(
        Place $place,
        VisitorPreferencesDTO $preferences
    ): bool {
        if (!$place->is_available) {
            return false;
        }

        if (
            $preferences->hasChildren === true
            && !$place->suitable_for_children
        ) {
            return false;
        }

        if (
            $preferences->budget !== null
            && $place->average_cost > $preferences->budget
        ) {
            return false;
        }

        if (
            $preferences->availableMinutes !== null
            && $place->duration_minutes
                > $preferences->availableMinutes
        ) {
            return false;
        }

        if (
            !$this->supportsAccessibility(
                $place,
                $preferences->accessibilityRequirements
            )
        ) {
            return false;
        }

        return true;
    }

    private function supportsAccessibility(
        Place $place,
        array $requirements
    ): bool {
        if (empty($requirements)) {
            return true;
        }

        $available = $place
            ->accessibilityFeatures
            ->pluck('slug')
            ->all();

        foreach ($requirements as $requirement) {
            if (!in_array($requirement, $available, true)) {
                return false;
            }
        }

        return true;
    }

    private function calculateScore(
        Place $place,
        VisitorPreferencesDTO $preferences
    ): int {
        $score = 0;

        $tags = collect($place->tags ?? [])
            ->map(fn ($tag) => mb_strtolower($tag));

        $category = mb_strtolower(
            $place->category?->slug ?? ''
        );

        foreach ($preferences->interests as $interest) {
            $interest = mb_strtolower($interest);

            if (
                $tags->contains($interest)
                || $category === $interest
            ) {
                $score += 25;
            }
        }

        foreach ($preferences->moods as $mood) {
            if (
                $tags->contains(
                    mb_strtolower($mood)
                )
            ) {
                $score += 15;
            }
        }

        if (
            $preferences->hasChildren === true
            && $place->suitable_for_children
        ) {
            $score += 15;
        }

        if (
            $preferences->intensity !== null
            && $place->intensity
                === $preferences->intensity
        ) {
            $score += 10;
        }

        if (
            $preferences->budget !== null
            && $place->average_cost
                <= ($preferences->budget * 0.30)
        ) {
            $score += 10;
        }

        if (!$place->is_outdoor) {
            $score += 2;
        }

        return $score;
    }

    private function orderByProximity(
        array $stops
    ): array {
        if (count($stops) <= 2) {
            return $stops;
        }

        $ordered = [
            array_shift($stops),
        ];

        while (!empty($stops)) {
            $current = end($ordered);

            $closestIndex = null;
            $closestDistance = PHP_FLOAT_MAX;

            foreach ($stops as $index => $stop) {
                $distance = $this->distance(
                    $current,
                    $stop
                );

                if ($distance < $closestDistance) {
                    $closestDistance = $distance;
                    $closestIndex = $index;
                }
            }

            $ordered[] = $stops[$closestIndex];

            unset($stops[$closestIndex]);

            $stops = array_values($stops);
        }

        return $ordered;
    }

    private function distance(
        ItineraryStopDTO $a,
        ItineraryStopDTO $b
    ): float {
        $earthRadius = 6371;

        $latFrom = deg2rad($a->latitude);
        $lonFrom = deg2rad($a->longitude);

        $latTo = deg2rad($b->latitude);
        $lonTo = deg2rad($b->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $value =
            sin($latDelta / 2) ** 2
            + cos($latFrom)
            * cos($latTo)
            * sin($lonDelta / 2) ** 2;

        return 2
            * $earthRadius
            * asin(min(1, sqrt($value)));
    }
}