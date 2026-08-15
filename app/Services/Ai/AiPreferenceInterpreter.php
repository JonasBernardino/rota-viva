<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;
use App\DTOs\VisitorPreferencesDTO;
use RuntimeException;

class AiPreferenceInterpreter
{
    public function __construct(
        private readonly AiProvider $ai,
        private readonly LocalPreferenceInterpreter $fallback,
    ) {}

    public function interpret(
        string $description
    ): VisitorPreferencesDTO {
        try {
            $result = $this->ai->generateStructured(
                systemPrompt: $this->systemPrompt(),
                userPrompt: $description,
                schema: $this->schema(),
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return $this->fallback->interpret($description);
        }

        return VisitorPreferencesDTO::fromArray($result);
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Você é responsável por interpretar preferências de visitantes
para uma plataforma municipal de turismo chamada Rota Viva.

Sua única responsabilidade é extrair critérios estruturados
a partir da descrição do visitante.

Regras:

- Não recomende atrativos.
- Não invente lugares.
- Não invente preços.
- Não invente horários.
- Não invente informações que o usuário não forneceu.
- Converta horas para minutos.
- Converta valores monetários para números.
- Se o usuário mencionar criança ou filhos, marque has_children como true.
- Se uma informação não estiver disponível, utilize null ou array vazio.
- Liste informações relevantes ausentes em missing_information.

Possíveis categorias de interesse incluem:
cultura, historia, gastronomia, natureza, religiao,
aventura, lazer, arte, patrimonio e experiencias_locais.

Possíveis intensidades:
low, medium, high.

Possíveis meios de transporte:
walking, car, public_transport, bicycle.

Retorne exclusivamente a estrutura solicitada.
PROMPT;
    }

    private function schema(): array
    {
        return [
            'type' => 'object',

            'properties' => [
                'moods' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],

                'interests' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],

                'available_minutes' => [
                    'type' => ['integer', 'null'],
                ],

                'budget' => [
                    'type' => ['number', 'null'],
                ],

                'has_children' => [
                    'type' => ['boolean', 'null'],
                ],

                'transport' => [
                    'type' => ['string', 'null'],
                ],

                'accessibility_requirements' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],

                'intensity' => [
                    'type' => ['string', 'null'],
                ],

                'missing_information' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
            ],

            'required' => [
                'moods',
                'interests',
                'available_minutes',
                'budget',
                'has_children',
                'transport',
                'accessibility_requirements',
                'intensity',
                'missing_information',
            ],
        ];
    }
}
