<?php

namespace App\Services\Itinerary;

use App\Contracts\AtrativoRepository;
use App\DTOs\GeneratedItineraryDTO;
use App\DTOs\ItineraryStopDTO;
use App\DTOs\VisitorPreferencesDTO;
use App\Models\Atrativo;
use Carbon\Carbon;
use RuntimeException;

class ItineraryService
{
    public function __construct(
        private readonly AtrativoRepository $places,
    ) {}

    public function generate(
        VisitorPreferencesDTO $preferences
    ): GeneratedItineraryDTO {
        $places = $this->places->available();

        $candidates = $places
            ->filter(
                fn (Atrativo $place) => $this->passesRequiredFilters(
                    $place,
                    $preferences
                )
            )
            ->map(fn (Atrativo $place) => [
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
            /** @var Atrativo $place */
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
                ! $place->isOpenDuring(
                    $expectedStart,
                    $place->duration_minutes
                )
            ) {
                continue;
            }

            $selected[] = new ItineraryStopDTO(
                placeId: $place->id,
                name: $place->name,
                category: $place->category?->nome ?? $place->category?->name ?? 'Experiência',
                durationMinutes: $place->duration_minutes,
                estimatedCost: $place->average_cost,
                latitude: $place->latitude,
                longitude: $place->longitude,
                isOutdoor: $place->is_outdoor,
                score: $candidate['score'],
            );

            $totalDuration = $newDuration;
            $totalCost = $newCost;
        }

        if ($selected === []) {
            throw new RuntimeException(
                'Não foi possível encontrar atrativos compatíveis com os critérios informados.'
            );
        }

        return new GeneratedItineraryDTO(
            stops: $selected,
            totalDurationMinutes: $totalDuration,
            totalEstimatedCost: $totalCost,
        );
    }

    private function passesRequiredFilters(
        Atrativo $place,
        VisitorPreferencesDTO $preferences
    ): bool {
        if (
            $preferences->hasChildren === true
            && ! $place->suitable_for_children
        ) {
            return false;
        }

        if ($preferences->accessibilityRequirements !== []) {
            $placeFeatures = $place
                ->recursosAcessibilidade
                ->pluck('slug')
                ->all();

            foreach (
                $preferences->accessibilityRequirements as $requiredFeature
            ) {
                if (! in_array($requiredFeature, $placeFeatures, true)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function calculateScore(
        Atrativo $place,
        VisitorPreferencesDTO $preferences
    ): float {
        $score = 0.0;

        $categorySlug = $place->categoria?->slug ?? $place->category?->slug ?? '';

        if (
            in_array(
                $categorySlug,
                $preferences->interests,
                true
            )
        ) {
            $score += 30.0;
        }

        foreach ($preferences->interests as $interest) {
            if (
                in_array(
                    $interest,
                    $place->tags ?? [],
                    true
                )
            ) {
                $score += 15.0;
            }
        }

        foreach ($preferences->moods as $mood) {
            if (
                in_array(
                    $mood,
                    $place->tags ?? [],
                    true
                )
            ) {
                $score += 10.0;
            }
        }

        if (
            $preferences->intensity !== null
            && $place->intensidade === $preferences->intensity
        ) {
            $score += 10.0;
        }

        if (
            $preferences->budget !== null
            && $preferences->budget > 0
        ) {
            $costProportion =
                $place->average_cost / $preferences->budget;

            if ($costProportion <= 0.3) {
                $score += 10.0;
            }
        }

        return $score;
    }
}
