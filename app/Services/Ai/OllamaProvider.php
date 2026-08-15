<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class OllamaProvider implements AiProvider
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $model,
        private readonly int $timeoutSeconds = 8,
    ) {}

    public function generateStructured(
        string $systemPrompt,
        string $userPrompt,
        array $schema
    ): array {
        $url = rtrim($this->baseUrl, '/').'/api/chat';

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->connectTimeout(5)
                ->timeout($this->timeoutSeconds)
                ->post($url, [
                    'model' => $this->model,
                    'stream' => false,
                    'format' => $schema,
                    'options' => [
                        'temperature' => 0,
                    ],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt."\n\nResponda somente com JSON valido, sem Markdown.",
                        ],
                        [
                            'role' => 'user',
                            'content' => $userPrompt,
                        ],
                    ],
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException(
                'Servico local do Ollama indisponivel.',
                previous: $exception,
            );
        } catch (Throwable $exception) {
            throw new RuntimeException(
                'Nao foi possivel consultar o Ollama local.',
                previous: $exception,
            );
        }

        if ($response->failed()) {
            throw new RuntimeException(
                sprintf(
                    'Erro ao consultar Ollama. HTTP %s.',
                    $response->status()
                )
            );
        }

        $content = data_get($response->json(), 'message.content');

        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException(
                'Ollama nao retornou conteudo estruturado.'
            );
        }

        $decoded = json_decode($content, true);

        if (! is_array($decoded)) {
            throw new RuntimeException(
                'Resposta invalida retornada pelo Ollama.'
            );
        }

        return $decoded;
    }
}
