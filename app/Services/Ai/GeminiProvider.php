<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiProvider implements AiProvider
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
    ) {
    }

    public function generateStructured(
        string $systemPrompt,
        string $userPrompt,
        array $schema
    ): array {
        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
            $this->model
        );

        $response = Http::withHeaders([
            'x-goog-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->timeout(20)
            ->post($url, [
                'system_instruction' => [
                    'parts' => [
                        [
                            'text' => $systemPrompt,
                        ],
                    ],
                ],

                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            [
                                'text' => $userPrompt,
                            ],
                        ],
                    ],
                ],

                'generation_config' => [
                    'response_mime_type' => 'application/json',
                    'response_json_schema' => $schema,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Erro ao consultar Gemini: '.$response->body()
            );
        }

        $payload = $response->json();

        $text = data_get(
            $payload,
            'candidates.0.content.parts.0.text'
        );

        if (!$text) {
            throw new RuntimeException(
                'Gemini não retornou conteúdo estruturado.'
            );
        }

        $decoded = json_decode($text, true);

        if (!is_array($decoded)) {
            throw new RuntimeException(
                'Resposta inválida retornada pelo Gemini.'
            );
        }

        return $decoded;
    }
}