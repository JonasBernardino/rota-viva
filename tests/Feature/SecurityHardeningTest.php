<?php

namespace Tests\Feature;

use App\Models\Municipio;
use App\Models\User;
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
}
