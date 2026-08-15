<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;
use App\DTOs\AdaptationNarrativeDTO;
use App\DTOs\AdaptedItineraryDTO;
use App\Models\Itinerary;
use Throwable;

class AiAdaptationWriter
{
    public function __construct(
        private readonly AiProvider $ai,
    ) {}

    public function explainRainAdaptation(
        Itinerary $original,
        AdaptedItineraryDTO $adapted
    ): AdaptationNarrativeDTO {
        try {
            $result =
                $this->ai
                    ->generateStructured(
                        systemPrompt: $this->systemPrompt(),

                        userPrompt: $this->buildPrompt(
                            $original,
                            $adapted
                        ),

                        schema: $this->schema(),
                    );

            return new AdaptationNarrativeDTO(
                title: $result['title']
                    ?? 'Sua rota foi adaptada',

                summary: $result['summary']
                    ?? 'Ajustamos sua rota por causa da chuva.',

                changes: $result['changes']
                    ?? [],
            );
        } catch (Throwable) {
            return $this->fallback();
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Você explica adaptações realizadas em uma rota turística.

A rota já foi recalculada e validada pelo backend.

Sua responsabilidade é apenas explicar as mudanças.

Regras:

- Não altere os locais.
- Não invente locais.
- Não invente preços.
- Não invente horários.
- Explique claramente quais pontos saíram.
- Explique quais pontos entraram.
- Explique que a mudança ocorreu devido à chuva.
- Destaque que tempo, orçamento e preferências foram preservados.
- Use linguagem simples, acolhedora e objetiva.
PROMPT;
    }

    private function buildPrompt(
        Itinerary $original,
        AdaptedItineraryDTO $adapted
    ): string {
        $original->loadMissing(
            'items.place'
        );

        return json_encode([
            'event' => 'RAIN_STARTED',

            'original_route' => $original->items
                ->map(
                    fn ($item) => [
                        'place_id' => $item->place_id,

                        'name' => $item->place->name,

                        'is_outdoor' => $item
                            ->place
                            ->is_outdoor,
                    ]
                )
                ->values()
                ->all(),

            'removed_place_ids' => $adapted->removedPlaceIds,

            'added_place_ids' => $adapted->addedPlaceIds,

            'new_route' => array_map(
                fn ($stop) => [
                    'place_id' => $stop->placeId,

                    'name' => $stop->name,

                    'duration_minutes' => $stop
                        ->durationMinutes,

                    'estimated_cost' => $stop
                        ->estimatedCost,
                ],
                $adapted->stops
            ),

            'total_duration_minutes' => $adapted
                ->totalDurationMinutes,

            'total_estimated_cost' => $adapted
                ->totalEstimatedCost,

        ], JSON_UNESCAPED_UNICODE);
    }

    private function schema(): array
    {
        return [
            'type' => 'object',

            'properties' => [
                'title' => [
                    'type' => 'string',
                ],

                'summary' => [
                    'type' => 'string',
                ],

                'changes' => [
                    'type' => 'array',

                    'items' => [
                        'type' => 'object',

                        'properties' => [
                            'place_id' => [
                                'type' => 'integer',
                            ],

                            'message' => [
                                'type' => 'string',
                            ],
                        ],

                        'required' => [
                            'place_id',
                            'message',
                        ],
                    ],
                ],
            ],

            'required' => [
                'title',
                'summary',
                'changes',
            ],
        ];
    }

    private function fallback(): AdaptationNarrativeDTO
    {
        return new AdaptationNarrativeDTO(
            title: 'Sua rota foi adaptada à chuva',

            summary: 'Substituímos atividades externas por opções cobertas compatíveis com sua experiência.',

            changes: [],
        );
    }
}
