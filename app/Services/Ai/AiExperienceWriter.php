<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;
use App\DTOs\ExperienceNarrativeDTO;
use App\DTOs\GeneratedItineraryDTO;
use App\DTOs\VisitorPreferencesDTO;
use Throwable;

class AiExperienceWriter
{
    public function __construct(
        private readonly AiProvider $ai,
    ) {}

    public function write(
        VisitorPreferencesDTO $preferences,
        GeneratedItineraryDTO $itinerary
    ): ExperienceNarrativeDTO {
        try {
            $result = $this->ai->generateStructured(
                systemPrompt: $this->systemPrompt(),
                userPrompt: $this->buildPrompt(
                    $preferences,
                    $itinerary
                ),
                schema: $this->schema(),
            );

            return $this->fromAiResponse(
                $result,
                $itinerary
            );
        } catch (Throwable) {
            return $this->fallback($itinerary);
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Você escreve explicações para rotas turísticas
da plataforma municipal Rota Viva.

Os locais já foram selecionados e validados pelo backend.

Regras obrigatórias:

- Nunca invente locais.
- Nunca altere os place_id recebidos.
- Nunca adicione novas paradas.
- Nunca invente preços ou horários.
- Explique somente por que os locais selecionados
  combinam com as preferências do visitante.
- Utilize linguagem clara, acolhedora e objetiva.
PROMPT;
    }

    private function buildPrompt(
        VisitorPreferencesDTO $preferences,
        GeneratedItineraryDTO $itinerary
    ): string {
        return json_encode([
            'visitor' => [
                'moods' => $preferences->moods,
                'interests' => $preferences->interests,
                'available_minutes' => $preferences->availableMinutes,
                'budget' => $preferences->budget,
                'has_children' => $preferences->hasChildren,
                'intensity' => $preferences->intensity,
                'accessibility' => $preferences
                    ->accessibilityRequirements,
            ],

            'selected_stops' => array_map(
                fn ($stop) => [
                    'place_id' => $stop->placeId,
                    'name' => $stop->name,
                    'category' => $stop->category,
                    'duration_minutes' => $stop->durationMinutes,
                    'estimated_cost' => $stop->estimatedCost,
                ],
                $itinerary->stops
            ),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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

                'stops' => [
                    'type' => 'array',

                    'items' => [
                        'type' => 'object',

                        'properties' => [
                            'place_id' => [
                                'type' => 'integer',
                            ],

                            'reason' => [
                                'type' => 'string',
                            ],
                        ],

                        'required' => [
                            'place_id',
                            'reason',
                        ],
                    ],
                ],
            ],

            'required' => [
                'title',
                'summary',
                'stops',
            ],
        ];
    }

    private function fromAiResponse(
        array $result,
        GeneratedItineraryDTO $itinerary
    ): ExperienceNarrativeDTO {
        $validIds = collect($itinerary->stops)
            ->pluck('placeId')
            ->all();

        $reasons = [];

        foreach ($result['stops'] ?? [] as $stop) {
            $placeId = $stop['place_id'] ?? null;

            if (
                $placeId !== null
                && in_array($placeId, $validIds, true)
            ) {
                $reasons[$placeId] =
                    $stop['reason'];
            }
        }

        return new ExperienceNarrativeDTO(
            title: $result['title']
                ?? 'Sua experiência no Rota Viva',

            summary: $result['summary']
                ?? 'Uma rota criada a partir das suas preferências.',

            reasons: $reasons,
        );
    }

    private function fallback(
        GeneratedItineraryDTO $itinerary
    ): ExperienceNarrativeDTO {
        $reasons = [];

        foreach ($itinerary->stops as $stop) {
            $reasons[$stop->placeId] =
                'Selecionado por ser compatível com seu perfil, tempo e orçamento.';
        }

        return new ExperienceNarrativeDTO(
            title: 'Uma experiência do seu jeito',
            summary: 'Criamos uma rota compatível com as preferências informadas.',
            reasons: $reasons,
        );
    }
}
