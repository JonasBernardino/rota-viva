<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class GeminiProvider implements AiProvider
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
    ) {}

    public function generateStructured(
        string $systemPrompt,
        string $userPrompt,
        array $schema
    ): array {
        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
            $this->model
        );

        try {
            $response = Http::withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
                ->connectTimeout(5)
                ->timeout(12)
                ->retry(
                    times: 2,
                    sleepMilliseconds: 500,
                    throw: false,
                )
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

        } catch (ConnectionException $exception) {
            throw new RuntimeException(
                'Serviço de inteligência artificial indisponível.',
                previous: $exception,
            );
        } catch (Throwable $exception) {
            throw new RuntimeException(
                'Não foi possível consultar o serviço de inteligência artificial.',
                previous: $exception,
            );
        }

        if ($response->failed()) {
            throw new RuntimeException(
                sprintf(
                    'Erro ao consultar Gemini. HTTP %s.',
                    $response->status()
                )
            );
        }

        $payload = $response->json();

        $text = data_get(
            $payload,
            'candidates.0.content.parts.0.text'
        );

        if (! $text) {
            throw new RuntimeException(
                'Gemini não retornou conteúdo estruturado.'
            );
        }

        $decoded = json_decode(
            $text,
            true
        );

        if (! is_array($decoded)) {
            throw new RuntimeException(
                'Resposta inválida retornada pelo Gemini.'
            );
        }

        return $decoded;
    }
}
