<?php

namespace Tests\Feature;

use App\Models\Municipio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class MunicipalityAppearanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_manager_can_update_municipality_branding_and_home_banner(): void
    {
        Storage::fake('public');

        $lucena = Municipio::where('slug', 'lucena')->firstOrFail();
        $manager = User::factory()->managerFor($lucena)->create();

        $response = $this
            ->actingAs($manager)
            ->put('http://lucena.rota-viva.test/gestor/aparencia', [
                'brand_name' => 'Rota Viva Lucena',
                'brand_logo' => UploadedFile::fake()->image('logo.png', 256, 256),
                'hero_eyebrow' => 'Turismo oficial',
                'hero_title' => 'Viva Lucena do seu jeito',
                'hero_description' => 'Experiências oficiais para conhecer Lucena com confiança.',
                'hero_image' => UploadedFile::fake()->image('banner.jpg', 1600, 900),
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
                'local_economy_image' => UploadedFile::fake()->image('economia.jpg', 1200, 800),
                'local_economy_image_alt' => 'Produtora local de Lucena',
            ]);

        $response
            ->assertRedirect(route('admin.appearance.edit'))
            ->assertSessionHas('status');

        $lucena->refresh();

        $this->assertSame('Rota Viva Lucena', $lucena->brand_name);
        $this->assertSame('Viva Lucena do seu jeito', $lucena->hero_title);
        $this->assertSame(['Praia', 'Cultura', 'Família'], $lucena->hero_card_tags);
        $this->assertSame('Rotas que fortalecem Lucena', $lucena->local_economy_title);
        Storage::disk('public')->assertExists($lucena->brand_logo_path);
        Storage::disk('public')->assertExists($lucena->hero_image_path);
        Storage::disk('public')->assertExists($lucena->local_economy_image_path);

        $this->get('http://lucena.rota-viva.test')
            ->assertOk()
            ->assertSee('Rota Viva Lucena')
            ->assertSee('Turismo oficial')
            ->assertSee('Viva Lucena do seu jeito')
            ->assertSee('Experiências oficiais para conhecer Lucena com confiança.')
            ->assertSee('Lucena em movimento')
            ->assertSee('Praia')
            ->assertSee('Economia viva')
            ->assertSee('Rotas que fortalecem Lucena')
            ->assertSee('Ver produtores locais');
    }

    public function test_manager_cannot_update_another_municipality_appearance(): void
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

        $this
            ->actingAs($manager)
            ->get('http://cabedelo.rota-viva.test/gestor/aparencia')
            ->assertForbidden();
    }
}
