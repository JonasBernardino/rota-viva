<?php

namespace Tests\Feature;

use App\Contracts\AiProvider;
use App\Models\Roteiro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ItineraryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_route_builder_page_loads_successfully(): void
    {
        $response = $this->get(route('routes.create'));

        $response->assertOk()
            ->assertSee('Como você quer viver a cidade hoje?')
            ->assertSee('Continuar');
    }

    public function test_creates_itinerary_and_adapts_for_rain(): void
    {
        // 1. Setup mock AI Provider
        $mockAi = Mockery::mock(AiProvider::class);

        // Mock preference interpretation
        $mockAi->shouldReceive('generateStructured')
            ->andReturnUsing(function ($systemPrompt, $userPrompt) {
                if (str_contains($systemPrompt, 'interpretar preferências')) {
                    return [
                        'moods' => ['tranquilo', 'cultural'],
                        'interests' => ['cultura', 'patrimonio-historico'],
                        'available_minutes' => 240,
                        'budget' => 150.0,
                        'has_children' => true,
                        'transport' => 'car',
                        'accessibility_requirements' => [],
                        'intensity' => 'low',
                        'missing_information' => [],
                    ];
                }

                if (str_contains($systemPrompt, 'narrativa')) {
                    return [
                        'title' => 'Entre Cultura e Tranquilidade',
                        'summary' => 'Uma experiência tranquila e cultural para toda a família.',
                        'stops' => [
                            ['place_id' => 1, 'reason' => 'Local coberto, cultural e ótimo para crianças.'],
                            ['place_id' => 2, 'reason' => 'Patrimônio histórico imperdível.'],
                        ],
                    ];
                }

                if (str_contains($systemPrompt, 'mudança de contexto por chuva')) {
                    return [
                        'title' => 'Rota Adaptada: Cultura Protegida da Chuva',
                        'summary' => 'Substituímos as atividades ao ar livre por opções cobertas.',
                        'changes' => [
                            ['place_id' => 2, 'action' => 'REMOVED', 'message' => 'Substituído por ser ar livre.'],
                            ['place_id' => 3, 'action' => 'ADDED', 'message' => 'Adicionado espaço cultural coberto.'],
                        ],
                    ];
                }

                return [];
            });

        $this->app->instance(AiProvider::class, $mockAi);

        // 2. Submit route request
        $response = $this->post(route('routes.store'), [
            'description' => 'Quero uma experiência tranquila e cultural, estou com uma criança, tenho quatro horas e orçamento de R$ 150.',
        ]);

        $itinerary = Roteiro::latest('id')->first();
        $this->assertNotNull($itinerary);

        $response->assertRedirect(route('routes.show', $itinerary));

        // 3. View created route
        $viewResponse = $this->get(route('routes.show', $itinerary));
        $viewResponse->assertOk()
            ->assertSee('Sua realidade mudou?')
            ->assertSee('Começou a chover');

        // 4. Trigger rain adaptation
        $rainResponse = $this->post(route('routes.adapt.rain', $itinerary));
        $rainResponse->assertRedirect();

        // 5. Follow adaptation view
        $adaptation = $itinerary->adaptacoes()->first();
        $this->assertNotNull($adaptation);

        $adaptationResponse = $this->get(route('routes.adaptation.show', [
            'itinerary' => $itinerary,
            'adaptation' => $adaptation,
        ]));

        $adaptationResponse->assertOk()
            ->assertSee('Rota adaptada')
            ->assertSee('Sua rota continua fazendo sentido.')
            ->assertSee('Veja como sua rota mudou')
            ->assertSee('Antes')
            ->assertSee('Agora');
    }
}
