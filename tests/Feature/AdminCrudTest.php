<?php

namespace Tests\Feature;

use App\Models\Atrativo;
use App\Models\Categoria;
use App\Models\Estabelecimento;
use App\Models\Evento;
use App\Models\MidiaAtrativo;
use App\Models\Municipio;
use App\Models\Roteiro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $lucena = Municipio::where('slug', 'lucena')->firstOrFail();

        $this->manager = User::factory()->managerFor($lucena)->create();
    }

    public function test_manager_can_open_crud_forms(): void
    {
        $this->actingAs($this->manager)
            ->get(route('admin.tourist-spots.create'))
            ->assertOk()
            ->assertSee('Novo cadastro');

        $this->actingAs($this->manager)
            ->get(route('admin.tourist-spots.edit', Atrativo::firstOrFail()->id))
            ->assertOk()
            ->assertSee('Editar cadastro');

        $this->actingAs($this->manager)
            ->get(route('admin.establishments.create'))
            ->assertOk();

        $this->actingAs($this->manager)
            ->get(route('admin.events.create'))
            ->assertOk();

        $this->actingAs($this->manager)
            ->get(route('admin.official-itineraries.create'))
            ->assertOk();
    }

    public function test_manager_can_create_update_and_delete_tourist_spot(): void
    {
        Storage::fake('public');

        $category = Categoria::firstOrFail();

        $this->actingAs($this->manager)
            ->post(route('admin.tourist-spots.store'), [
                'categoria_id' => $category->id,
                'nome' => 'Museu da Maré',
                'slug' => 'museu-da-mare',
                'descricao' => 'Espaço cultural demonstrativo.',
                'latitude' => -6.901,
                'longitude' => -34.861,
                'duracao_minutos' => 45,
                'custo_medio' => 12,
                'intensidade' => 'low',
                'tags' => 'cultura, memoria',
                'adequado_criancas' => 1,
                'is_disponivel' => 1,
                'imagem' => UploadedFile::fake()->image('museu.jpg', 1200, 800),
                'imagem_alt' => 'Fachada do Museu da Maré',
            ])
            ->assertRedirect(route('admin.tourist-spots.index'));

        $place = Atrativo::where('slug', 'museu-da-mare')->firstOrFail();
        $media = MidiaAtrativo::where('atrativo_id', $place->id)->firstOrFail();

        $this->assertSame(['cultura', 'memoria'], $place->tags);
        $this->assertSame('Fachada do Museu da Maré', $media->descricao_acessibilidade);
        Storage::disk('public')->assertExists($media->url);

        $this->actingAs($this->manager)
            ->put(route('admin.tourist-spots.update', $place->id), [
                'categoria_id' => $category->id,
                'nome' => 'Museu da Maré Viva',
                'slug' => 'museu-da-mare-viva',
                'descricao' => 'Espaço cultural atualizado.',
                'latitude' => -6.902,
                'longitude' => -34.862,
                'duracao_minutos' => 60,
                'custo_medio' => 15,
                'intensidade' => 'medium',
                'tags' => 'cultura',
                'adequado_criancas' => 1,
                'is_disponivel' => 1,
            ])
            ->assertRedirect(route('admin.tourist-spots.index'));

        $this->assertDatabaseHas('atrativos', [
            'id' => $place->id,
            'nome' => 'Museu da Maré Viva',
            'slug' => 'museu-da-mare-viva',
        ]);

        $this->actingAs($this->manager)
            ->delete(route('admin.tourist-spots.destroy', $place->id))
            ->assertRedirect(route('admin.tourist-spots.index'));

        $this->assertDatabaseMissing('atrativos', ['id' => $place->id]);
    }

    public function test_manager_can_create_update_and_delete_establishment(): void
    {
        Storage::fake('public');

        $this->actingAs($this->manager)
            ->post(route('admin.establishments.store'), [
                'nome' => 'Café da Praia',
                'slug' => 'cafe-da-praia',
                'tipo_estabelecimento' => 'gastronomia',
                'descricao' => 'Café regional perto da orla.',
                'endereco' => 'Rua Beira Mar, 10',
                'bairro' => 'Centro',
                'latitude' => -6.903,
                'longitude' => -34.863,
                'telefone' => '(83) 3000-0000',
                'whatsapp' => '(83) 99999-0000',
                'faixa_preco' => '$$',
                'tem_selo_qualidade' => 1,
                'status_validacao' => 'approved',
                'imagem' => UploadedFile::fake()->image('cafe.jpg', 1200, 800),
            ])
            ->assertRedirect(route('admin.establishments.index'));

        $business = Estabelecimento::where('slug', 'cafe-da-praia')->firstOrFail();

        $this->assertTrue($business->tem_selo_qualidade);
        $this->assertNotNull($business->imagem_capa);
        Storage::disk('public')->assertExists($business->imagem_capa);

        $this->actingAs($this->manager)
            ->put(route('admin.establishments.update', $business->id), [
                'nome' => 'Café da Praia Viva',
                'slug' => 'cafe-da-praia-viva',
                'tipo_estabelecimento' => 'gastronomia',
                'descricao' => 'Café regional atualizado.',
                'endereco' => 'Rua Beira Mar, 11',
                'bairro' => 'Centro',
                'faixa_preco' => '$$$',
                'status_validacao' => 'approved',
            ])
            ->assertRedirect(route('admin.establishments.index'));

        $this->assertDatabaseHas('estabelecimentos', [
            'id' => $business->id,
            'nome' => 'Café da Praia Viva',
        ]);

        $this->actingAs($this->manager)
            ->delete(route('admin.establishments.destroy', $business->id))
            ->assertRedirect(route('admin.establishments.index'));

        $this->assertDatabaseMissing('estabelecimentos', ['id' => $business->id]);
    }

    public function test_manager_can_create_update_and_delete_event(): void
    {
        Storage::fake('public');

        $this->actingAs($this->manager)
            ->post(route('admin.events.store'), [
                'nome' => 'Festival da Cultura Viva',
                'slug' => 'festival-da-cultura-viva',
                'descricao' => 'Programação cultural demonstrativa.',
                'nome_local' => 'Praça da Matriz',
                'endereco' => 'Centro',
                'inicia_em' => '2026-09-10 18:00:00',
                'termina_em' => '2026-09-10 22:00:00',
                'is_gratuito' => 1,
                'is_acessivel' => 1,
                'categoria' => 'cultural',
                'status' => 'scheduled',
                'imagem' => UploadedFile::fake()->image('festival.jpg', 1200, 800),
            ])
            ->assertRedirect(route('admin.events.index'));

        $event = Evento::where('slug', 'festival-da-cultura-viva')->firstOrFail();

        $this->assertNotNull($event->imagem_url);
        Storage::disk('public')->assertExists($event->imagem_url);

        $this->actingAs($this->manager)
            ->put(route('admin.events.update', $event->id), [
                'nome' => 'Festival da Cultura Viva Atualizado',
                'slug' => 'festival-da-cultura-viva-atualizado',
                'descricao' => 'Programação cultural atualizada.',
                'nome_local' => 'Centro Cultural',
                'inicia_em' => '2026-09-11 18:00:00',
                'categoria' => 'cultural',
                'status' => 'draft',
            ])
            ->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseHas('eventos', [
            'id' => $event->id,
            'status' => 'draft',
        ]);

        $this->actingAs($this->manager)
            ->delete(route('admin.events.destroy', $event->id))
            ->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseMissing('eventos', ['id' => $event->id]);
    }

    public function test_manager_can_create_update_and_delete_official_itinerary(): void
    {
        $this->actingAs($this->manager)
            ->post(route('admin.official-itineraries.store'), [
                'titulo' => 'Roteiro Oficial de Teste',
                'resumo' => 'Roteiro criado pelo painel municipal.',
                'duracao_total_minutos' => 120,
                'custo_total_estimado' => 30,
                'status' => 'official',
            ])
            ->assertRedirect(route('admin.official-itineraries.index'));

        $itinerary = Roteiro::where('titulo', 'Roteiro Oficial de Teste')->firstOrFail();

        $this->assertNotNull($itinerary->preferencia_visitante_id);

        $this->actingAs($this->manager)
            ->put(route('admin.official-itineraries.update', $itinerary->id), [
                'titulo' => 'Roteiro Oficial Atualizado',
                'resumo' => 'Roteiro atualizado pelo painel municipal.',
                'duracao_total_minutos' => 150,
                'custo_total_estimado' => 45,
                'status' => 'official',
            ])
            ->assertRedirect(route('admin.official-itineraries.index'));

        $this->assertDatabaseHas('roteiros', [
            'id' => $itinerary->id,
            'titulo' => 'Roteiro Oficial Atualizado',
        ]);

        $this->actingAs($this->manager)
            ->delete(route('admin.official-itineraries.destroy', $itinerary->id))
            ->assertRedirect(route('admin.official-itineraries.index'));

        $this->assertDatabaseMissing('roteiros', ['id' => $itinerary->id]);
    }
}
