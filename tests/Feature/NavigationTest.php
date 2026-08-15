<?php

namespace Tests\Feature;

use App\Models\Municipio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function publicPages(): array
    {
        return [
            'discover' => ['discover'],
            'experiences' => ['experiences'],
            'tourist spots' => ['tourist-spots.index'],
            'culture' => ['culture.index'],
            'city map' => ['city-map'],
            'tours' => ['tours.index'],
            'guides' => ['guides.index'],
            'official itineraries' => ['official-itineraries.index'],
            'agenda' => ['agenda.index'],
            'stays' => ['stays.index'],
            'dining' => ['dining.index'],
            'route builder' => ['routes.create'],
            'about' => ['about'],
            'contact' => ['contact'],
            'accessibility resources' => ['accessibility.resources'],
            'accessibility statement' => ['accessibility.statement'],
            'help' => ['help'],
            'privacy' => ['privacy'],
            'terms' => ['terms'],
        ];
    }

    #[DataProvider('publicPages')]
    public function test_public_pages_are_available(string $routeName): void
    {
        $this->get(route($routeName))->assertOk();
    }

    public function test_catalog_detail_pages_are_available(): void
    {
        $routes = [
            ['tourist-spots.show', 'igreja-nossa-senhora-da-guia'],
            ['culture.show', 'centro-cultural-e-memoria-de-lucena'],
            ['tours.show', 'guia-tiago-miriri-ecoturismo'],
            ['guides.show', 'guia-dona-marta-caminhos-da-guia'],
            ['official-itineraries.show', '1'],
            ['stays.show', 'pousada-recanto-dos-coqueiros'],
            ['dining.show', 'restaurante-sabores-da-guia'],
            ['agenda.show', 'festa-da-padroeira-nossa-senhora-da-guia-2026'],
        ];

        foreach ($routes as [$routeName, $slug]) {
            $this->get(route($routeName, $slug))->assertOk();
        }
    }

    public function test_tourist_spot_detail_shows_registered_media(): void
    {
        $this->get('http://lucena.rota-viva.test/pontos-turisticos/centro-cultural-e-memoria-de-lucena')
            ->assertOk()
            ->assertSee('/images/cultural-center.webp', false)
            ->assertSee('Centro Cultural e Memória de Lucena')
            ->assertSee('Fotos de Centro Cultural e Memória de Lucena');
    }

    public function test_tourist_spot_catalog_cards_show_registered_media(): void
    {
        $this->get('http://lucena.rota-viva.test/pontos-turisticos')
            ->assertOk()
            ->assertSee('/images/cultural-center.webp', false)
            ->assertSee('Centro Cultural e Memória de Lucena')
            ->assertSee('catalog-card__media', false);
    }

    public function test_city_map_cards_show_registered_media(): void
    {
        $this->get('http://lucena.rota-viva.test/mapa-da-cidade')
            ->assertOk()
            ->assertSee('/images/cultural-center.webp', false)
            ->assertSee('catalog-card__media', false)
            ->assertDontSee('catalog-card__placeholder', false);
    }

    public function test_landing_pages_do_not_show_numbered_link_cards(): void
    {
        $this->get(route('discover'))
            ->assertOk()
            ->assertDontSee('link-card__number', false);

        $this->get(route('experiences'))
            ->assertOk()
            ->assertDontSee('link-card__number', false);
    }

    public function test_official_itinerary_catalog_uses_route_place_media(): void
    {
        $this->get('http://lucena.rota-viva.test/roteiros-oficiais')
            ->assertOk()
            ->assertSee('/images/cultural-center.webp', false)
            ->assertSee('catalog-card__media', false);
    }

    public function test_home_navigation_points_to_public_pages(): void
    {
        $response = $this->get(route('home'));

        foreach (['discover', 'experiences', 'agenda.index', 'stays.index', 'dining.index', 'routes.create'] as $routeName) {
            $response->assertSee(route($routeName), false);
        }
    }

    public function test_home_has_functional_municipality_selector(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('municipalities.select'), false)
            ->assertSee('Selecione o município')
            ->assertSee('Lucena');
    }

    public function test_municipality_selector_keeps_options_inside_tenant_domain(): void
    {
        $this->withHeader('Host', 'lucena.rota-viva.test')
            ->get('/')
            ->assertOk()
            ->assertSee('Selecione o município')
            ->assertSee('value="lucena"', false)
            ->assertSee('Lucena');
    }

    public function test_selecting_municipality_redirects_to_configured_domain(): void
    {
        $this->withHeader('Host', 'rota-viva.test')
            ->get('/municipios/selecionar?municipality=lucena')
            ->assertRedirect('http://lucena.rota-viva.test');
    }

    public function test_manager_login_page_is_available_at_specific_admin_route(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Acesso ao painel');

        $this->assertSame(url('/gestor/entrar'), route('login'));
    }

    public function test_guest_navigation_does_not_show_login_or_manager_area(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertDontSee(route('login'), false)
            ->assertDontSee('Entrar')
            ->assertDontSee('Área do gestor')
            ->assertDontSee('Sair');
    }

    public function test_authenticated_regular_user_navigation_shows_logout_without_manager_area(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk()
            ->assertSee(route('logout'), false)
            ->assertSee('Sair')
            ->assertDontSee('Área do gestor');
    }

    public function test_authenticated_manager_navigation_shows_manager_area_and_logout(): void
    {
        $lucena = Municipio::where('slug', 'lucena')->firstOrFail();
        $manager = User::factory()->managerFor($lucena)->create();

        $response = $this->actingAs($manager)
            ->get('http://lucena.rota-viva.test');

        $response->assertOk()
            ->assertSee(route('admin.dashboard'), false)
            ->assertSee(route('logout'), false)
            ->assertSee('Área do gestor')
            ->assertSee('Sair');
    }

    public function test_authenticated_superadmin_navigation_shows_platform_area_and_logout(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)->get(route('home'));

        $response->assertOk()
            ->assertSee(route('platform.dashboard'), false)
            ->assertSee('Plataforma')
            ->assertSee('Sair')
            ->assertDontSee('Área do gestor');
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin_panel(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden()
            ->assertSee('Você não tem permissão para acessar esta área');
    }

    public function test_manager_can_access_admin_pages(): void
    {
        $lucena = Municipio::where('slug', 'lucena')->firstOrFail();
        $manager = User::factory()->managerFor($lucena)->create();

        $this->actingAs($manager)
            ->get('http://lucena.rota-viva.test/gestor')
            ->assertOk()
            ->assertSee('Painel Rota Viva');

        $this->actingAs($manager)
            ->get('http://lucena.rota-viva.test/gestor/tourist-spots')
            ->assertOk()
            ->assertSee('Pontos Turísticos');
    }

    public function test_manager_cannot_access_another_municipality_admin_panel(): void
    {
        $lucena = Municipio::where('slug', 'lucena')->firstOrFail();
        $manager = User::factory()->managerFor($lucena)->create();

        Municipio::create([
            'uuid' => (string) Str::uuid(),
            'nome' => 'Cabedelo',
            'slug' => 'cabedelo',
            'codigo_ibge' => '2503201',
            'uf' => 'PB',
            'nome_schema' => 'tenant_cabedelo',
            'status' => 'active',
            'fuso_horario' => 'America/Fortaleza',
        ])->dominios()->create([
            'dominio' => 'cabedelo.rota-viva.test',
            'is_principal' => true,
            'verificado_em' => now(),
        ]);

        $this->actingAs($manager)
            ->get('http://cabedelo.rota-viva.test/gestor')
            ->assertForbidden();
    }

    public function test_manager_can_login_with_valid_credentials(): void
    {
        $response = $this
            ->post('http://lucena.rota-viva.test/gestor/entrar', [
                'email' => 'gestor@lucena.pb.gov.br',
                'password' => '12345678',
            ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_manager_cannot_login_to_another_municipality(): void
    {
        Municipio::create([
            'uuid' => (string) Str::uuid(),
            'nome' => 'Cabedelo',
            'slug' => 'cabedelo',
            'codigo_ibge' => '2503201',
            'uf' => 'PB',
            'nome_schema' => 'tenant_cabedelo',
            'status' => 'active',
            'fuso_horario' => 'America/Fortaleza',
        ])->dominios()->create([
            'dominio' => 'cabedelo.rota-viva.test',
            'is_principal' => true,
            'verificado_em' => now(),
        ]);

        $response = $this
            ->from('http://cabedelo.rota-viva.test/gestor/entrar')
            ->post('http://cabedelo.rota-viva.test/gestor/entrar', [
                'email' => 'gestor@lucena.pb.gov.br',
                'password' => '12345678',
            ]);

        $response->assertRedirect('http://cabedelo.rota-viva.test/gestor/entrar');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_away_from_login_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('home'));
    }

    public function test_authenticated_manager_is_redirected_from_login_page_to_dashboard(): void
    {
        $lucena = Municipio::where('slug', 'lucena')->firstOrFail();
        $manager = User::factory()->managerFor($lucena)->create();

        $this->actingAs($manager)
            ->get('http://lucena.rota-viva.test/gestor/entrar')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_superadmin_is_redirected_from_login_page_to_platform_dashboard(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->get(route('login'))
            ->assertRedirect(route('platform.dashboard'));
    }

    public function test_superadmin_can_login_with_valid_credentials(): void
    {
        User::factory()->superadmin()->create([
            'email' => 'superadmin@example.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'superadmin@example.com',
            'password' => '12345678',
        ]);

        $response->assertRedirect(route('platform.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_regular_user_cannot_login_to_management_area(): void
    {
        User::factory()->create([
            'email' => 'visitante@example.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->from(route('login'))->post(route('login.post'), [
            'email' => 'visitante@example.com',
            'password' => '12345678',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->from(route('login'))->post(route('login.post'), [
            'email' => 'gestor@lucena.pb.gov.br',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $manager = User::where('email', 'gestor@lucena.pb.gov.br')->firstOrFail();

        $response = $this->actingAs($manager)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
