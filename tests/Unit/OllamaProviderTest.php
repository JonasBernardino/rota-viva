<?php

namespace Tests\Unit;

use App\Services\Ai\OllamaProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OllamaProviderTest extends TestCase
{
    public function test_it_generates_structured_output_from_ollama_chat_response(): void
    {
        Http::fake([
            'http://127.0.0.1:11434/api/chat' => Http::response([
                'message' => [
                    'content' => json_encode([
                        'title' => 'Entre cultura e tranquilidade',
                        'stops' => [
                            [
                                'place_id' => 12,
                                'reason' => 'Local coberto e cultural.',
                            ],
                        ],
                    ]),
                ],
            ]),
        ]);

        $provider = new OllamaProvider(
            baseUrl: 'http://127.0.0.1:11434',
            model: 'qwen2.5-coder',
        );

        $result = $provider->generateStructured(
            systemPrompt: 'Explique a rota.',
            userPrompt: 'Quero cultura e tranquilidade.',
            schema: [
                'type' => 'object',
                'properties' => [
                    'title' => ['type' => 'string'],
                    'stops' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'place_id' => ['type' => 'integer'],
                                'reason' => ['type' => 'string'],
                            ],
                            'required' => ['place_id', 'reason'],
                        ],
                    ],
                ],
                'required' => ['title', 'stops'],
            ],
        );

        $this->assertSame('Entre cultura e tranquilidade', $result['title']);
        $this->assertSame(12, $result['stops'][0]['place_id']);

        Http::assertSent(function ($request): bool {
            return $request->url() === 'http://127.0.0.1:11434/api/chat'
                && $request['model'] === 'qwen2.5-coder'
                && $request['stream'] === false
                && $request['options']['temperature'] === 0
                && $request['format']['type'] === 'object';
        });
    }
}
