<?php

namespace Tests\Feature;

use App\DTOs\VisitorPreferencesDTO;
use App\Models\Estabelecimento;
use App\Models\JourneyEvent;
use App\Models\Municipio;
use App\Models\User;
use App\Services\Analytics\JourneyAnalyticsService;
use App\Services\Tenant\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_login_is_rate_limited_after_repeated_failed_attempts(): void
    {
        $response = null;

        for ($attempt = 1; $attempt <= 6; $attempt++) {
            $response = $this->post('http://lucena.rota-viva.test/gestor/entrar', [
                'email' => 'gestor@lucena.test',
                'password' => 'senha-incorreta',
            ]);
        }

        $response?->assertTooManyRequests();
    }

    public function test_svg_upload_is_rejected_in_municipality_appearance(): void
    {
        Storage::fake('public');

        $lucena = Municipio::where('slug', 'lucena')->firstOrFail();
        $manager = User::factory()->managerFor($lucena)->create();

        $response = $this
            ->actingAs($manager)
            ->put('http://lucena.rota-viva.test/gestor/aparencia', [
                'brand_name' => 'Rota Viva Lucena',
                'brand_logo' => UploadedFile::fake()->create('logo.svg', 8, 'image/svg+xml'),
                'hero_eyebrow' => 'Turismo oficial',
                'hero_title' => 'Viva Lucena do seu jeito',
                'hero_description' => 'Experiências oficiais para conhecer Lucena com confiança.',
                'hero_image_alt' => 'Vista aérea de Lucena',
                'hero_search_placeholder' => 'Ex.: Quero praia, cultura e comida local',
                'hero_card_title' => 'Lucena em movimento',
                'hero_card_tags' => 'Praia, Cultura, Família',
                'local_economy_eyebrow' => 'Economia viva',
                'local_economy_title' => 'Rotas que fortalecem Lucena',
                'local_economy_description' => 'Cada percurso aproxima visitantes de produtores, guias e experiências locais.',
                'local_economy_stat' => '+ renda local no caminho',
                'local_economy_link_label' => 'Ver produtores locais',
                'local_economy_link_url' => '/quem-faz-a-cidade',
                'local_economy_image_alt' => 'Produtora local de Lucena',
            ]);

        $response->assertSessionHasErrors('brand_logo');
    }

    public function test_public_pages_include_security_headers(): void
    {
        $this->get('http://lucena.rota-viva.test')
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_journey_analytics_store_aggregated_preference_ranges(): void
    {
        $lucena = Municipio::where('slug', 'lucena')->firstOrFail();
        app(TenantManager::class)->switchTo($lucena);

        app(JourneyAnalyticsService::class)->trackRouteRequested(
            new VisitorPreferencesDTO(
                moods: ['tranquilo'],
                interests: ['cultura'],
                availableMinutes: 240,
                budget: 150.0,
                hasChildren: true,
                transport: 'car',
                accessibilityRequirements: [],
                intensity: 'low',
                missingInformation: ['telefone pessoal citado no texto'],
            )
        );

        $event = JourneyEvent::firstOrFail();

        $this->assertNull(data_get($event->payload, 'preferences.available_minutes'));
        $this->assertNull(data_get($event->payload, 'preferences.budget'));
        $this->assertSame('2h–4h', data_get($event->payload, 'preferences.duration_range'));
        $this->assertSame('R$ 51–150', data_get($event->payload, 'preferences.budget_range'));
        $this->assertSame(1, data_get($event->payload, 'preferences.missing_information_count'));
        $this->assertArrayNotHasKey('missing_information', $event->payload['preferences']);
    }

    public function test_entrepreneur_submission_is_pending_and_not_public_until_approved(): void
    {
        Storage::fake('public');

        $this->post('http://lucena.rota-viva.test/empreendedores', [
            'nome' => 'Bistrô Comunitário',
            'tipo_estabelecimento' => 'gastronomia',
            'descricao' => 'Cozinha local enviada por empreendedor para validação.',
            'endereco' => 'Rua da Praia, 100',
            'bairro' => 'Centro',
            'telefone' => '(83) 3000-0000',
            'faixa_preco' => '$$',
            'responsavel_nome' => 'Maria da Silva',
            'responsavel_email' => 'maria@example.com',
            'imagem' => UploadedFile::fake()->image('bistro.jpg', 1200, 800),
            'aceite_privacidade' => '1',
        ])->assertRedirect(route('entrepreneurs.create'));

        $business = Estabelecimento::where('nome', 'Bistrô Comunitário')->firstOrFail();

        $this->assertSame('pending', $business->status_validacao);
        $this->assertFalse($business->tem_selo_qualidade);
        $this->assertNotNull($business->imagem_capa);
        Storage::disk('public')->assertExists($business->imagem_capa);

        $this->get('http://lucena.rota-viva.test/onde-comer')
            ->assertOk()
            ->assertDontSee('Bistrô Comunitário');
    }
}
