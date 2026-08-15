<?php

namespace App\Services\Adaptation;

use App\Contracts\AtrativoRepository;
use App\DTOs\AdaptedItineraryDTO;
use App\DTOs\ItineraryStopDTO;
use App\Models\Atrativo;
use App\Models\Roteiro;
use RuntimeException;

class RouteAdaptationService
{
    public function __construct(
        private readonly AtrativoRepository $places,
    ) {}

    public function adaptForRain(
        Roteiro $itinerary
    ): AdaptedItineraryDTO {
        $itinerary->load([
            'itens.atrativo.categoria',
            'itens.atrativo.recursosAcessibilidade',
            'preferencia',
        ]);

        $removed = [];
        $kept = [];

        foreach ($itinerary->itens as $item) {
            $place = $item->atrativo;

            if ($place->is_ar_livre || $place->is_outdoor) {
                $removed[] = $place->id;

                continue;
            }

            $kept[] = new ItineraryStopDTO(
                placeId: $place->id,
                name: $place->nome,
                category: $place->categoria?->nome
                    ?? $place->category?->name
                    ?? 'Experiência',
                durationMinutes: $item->duracao_minutos,
                estimatedCost: $item->custo_estimado,
                latitude: $place->latitude,
                longitude: $place->longitude,
                isOutdoor: $place->is_ar_livre,
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
                name: $replacement->nome,
                category: $replacement->categoria?->nome
                    ?? $replacement->category?->name
                    ?? 'Experiência',
                durationMinutes: $replacement->duracao_minutos,
                estimatedCost: $replacement->custo_medio,
                latitude: $replacement->latitude,
                longitude: $replacement->longitude,
                isOutdoor: $replacement->is_ar_livre,
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
            $itinerary->preferencia?->minutos_disponiveis
            !== null
            && $duration
                > $itinerary
                    ->preferencia
                    ->minutos_disponiveis
        ) {
            throw new RuntimeException(
                'A rota adaptada ultrapassa o tempo disponível.'
            );
        }

        if (
            $itinerary->preferencia?->orcamento
            !== null
            && $cost
                > $itinerary
                    ->preferencia
                    ->orcamento
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
        Roteiro $itinerary,
        array $excludedIds,
    ) {
        $preferences =
            $itinerary->preferencia;

        return $this->places
            ->available()
            ->filter(
                fn (Atrativo $place) => ! $place->is_ar_livre
            )
            ->reject(
                fn (Atrativo $place) => in_array(
                    $place->id,
                    $excludedIds,
                    true
                )
            )
            ->filter(function (Atrativo $place) use (
                $preferences
            ) {
                if (
                    $preferences?->tem_criancas
                    && ! $place->adequado_criancas
                ) {
                    return false;
                }

                if (
                    $preferences?->orcamento !== null
                    && $place->custo_medio
                        > $preferences->orcamento
                ) {
                    return false;
                }

                return true;
            })
            ->sortByDesc(
                fn (Atrativo $place) => $this->calculateCompatibility(
                    $place,
                    $preferences
                )
            )
            ->values();
    }

    private function calculateCompatibility(
        Atrativo $place,
        $preferences
    ): int {
        $score = 0;

        $tags = collect(
            $place->tags ?? []
        )->map(
            fn ($tag) => mb_strtolower($tag)
        );

        foreach (
            $preferences?->interesses ?? [] as $interest
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
            $preferences?->humores ?? [] as $mood
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
            $preferences?->intensidade
            && $place->intensidade
                === $preferences->intensidade
        ) {
            $score += 10;
        }

        if (
            $preferences?->tem_criancas
            && $place->adequado_criancas
        ) {
            $score += 10;
        }

        return $score;
    }

    private function orderStops(
        Roteiro $itinerary,
        array $stops
    ): array {
        $originalOrder =
            $itinerary->itens
                ->pluck(
                    'posicao',
                    'atrativo_id'
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
