<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class DeepSeekProvider implements AiProvider
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
        private readonly string $baseUrl,
    ) {
    }

    public function generateStructured(
        string $systemPrompt,
        string $userPrompt,
        array $schema
    ): array {
        $url = rtrim($this->baseUrl, '/').'/chat/completions';

        $structuredSystemPrompt = $this->buildSystemPrompt(
            $systemPrompt,
            $schema
        );

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->connectTimeout(10)
                ->timeout(120)
                ->post($url, [
                    'model' => $this->model,

                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $structuredSystemPrompt,
                        ],
                        [
                            'role' => 'user',
                            'content' => $userPrompt,
                        ],
                    ],

                    'response_format' => [
                        'type' => 'json_object',
                    ],

                    /*
                     * Para esse caso não precisamos
                     * de raciocínio longo.
                     */
                    'thinking' => [
                        'type' => 'disabled',
                    ],

                    /*
                     * Evita resposta JSON truncada ou vazia.
                     */
                    'max_tokens' => 1000,

                    'temperature' => 0.1,

                    'stream' => false,
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException(
                'O serviço de inteligência artificial demorou demais para responder.',
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
                    'Erro ao consultar DeepSeek. HTTP %s. Resposta: %s',
                    $response->status(),
                    $response->body(),
                )
            );
        }

        $payload = $response->json();

        $content = data_get(
            $payload,
            'choices.0.message.content'
        );

        if (!$content || !is_string($content)) {
            throw new RuntimeException(
                sprintf(
                    'DeepSeek não retornou conteúdo estruturado. Resposta completa: %s',
                    $response->body(),
                )
            );
        }

        $decoded = json_decode(
            $content,
            true
        );

        if (
            json_last_error()
            !== JSON_ERROR_NONE
        ) {
            throw new RuntimeException(
                sprintf(
                    'DeepSeek retornou JSON inválido: %s. Conteúdo: %s',
                    json_last_error_msg(),
                    $content,
                )
            );
        }

        if (!is_array($decoded)) {
            throw new RuntimeException(
                'Resposta inválida retornada pela DeepSeek.'
            );
        }

        return $decoded;
    }

    private function buildSystemPrompt(
        string $systemPrompt,
        array $schema
    ): string {
        $schemaJson = json_encode(
            $schema,
            JSON_PRETTY_PRINT
                | JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
        );

        return <<<PROMPT
{$systemPrompt}

Responda obrigatoriamente em JSON.

Sua resposta deve conter somente um objeto JSON válido.

Não use Markdown.
Não use blocos de código.
Não escreva explicações antes ou depois do JSON.

Exemplo do formato esperado:

{
  "campo": "valor"
}

A estrutura obrigatória da resposta é:

{$schemaJson}

Regras:
- Use exatamente os nomes dos campos.
- Use null quando um valor opcional não estiver disponível.
- Use [] quando uma lista estiver vazia.
- Não invente informações.
- Números devem ser números.
- Booleanos devem ser true ou false.

Retorne somente JSON.
PROMPT;
    }
}