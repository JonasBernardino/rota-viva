<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Atrativo;
use App\Models\Categoria;
use App\Models\Estabelecimento;
use App\Models\Evento;
use App\Models\PreferenciaVisitante;
use App\Models\Roteiro;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display the admin management dashboard with real indicators.
     */
    public function dashboard(): View
    {
        $stats = [
            'places_count' => Atrativo::count(),
            'businesses_count' => Estabelecimento::count(),
            'validated_businesses_count' => Estabelecimento::where('tem_selo_qualidade', true)->count(),
            'events_count' => Evento::count(),
            'itineraries_count' => Roteiro::count(),
        ];

        $recentPlaces = Atrativo::with('categoria')->latest()->take(5)->get();
        $recentBusinesses = Estabelecimento::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentPlaces', 'recentBusinesses'));
    }

    /**
     * Display items for a specific administrative module.
     */
    public function module(Request $request, string $module): View
    {
        $config = $this->getModuleConfig($module);
        $items = $this->queryForModule($module)->latest()->get();

        return view('admin.module', [
            'module' => $module,
            'title' => $config['title'],
            'description' => $config['description'],
            'items' => $items,
            'publicRoute' => $this->publicRouteForModule($module),
        ]);
    }

    public function create(string $module): View
    {
        return $this->formView($module);
    }

    public function store(Request $request, string $module): RedirectResponse
    {
        $data = $this->validatedData($request, $module);

        DB::transaction(function () use ($data, $module): void {
            match ($this->modelTypeForModule($module)) {
                'place' => Atrativo::create($this->placePayload($data, $module)),
                'business' => Estabelecimento::create($this->businessPayload($data, $module)),
                'event' => Evento::create($this->eventPayload($data)),
                'itinerary' => $this->createOfficialItinerary($data),
            };
        });

        return redirect()
            ->route('admin.'.$module.'.index')
            ->with('status', 'Cadastro criado com sucesso.');
    }

    public function edit(int $id, string $module): View
    {
        $item = $this->findItem($module, $id);

        return $this->formView($module, $item);
    }

    public function update(Request $request, int $id, string $module): RedirectResponse
    {
        $item = $this->findItem($module, $id);
        $data = $this->validatedData($request, $module, $id);

        DB::transaction(function () use ($data, $item, $module): void {
            match ($this->modelTypeForModule($module)) {
                'place' => $item->update($this->placePayload($data, $module)),
                'business' => $item->update($this->businessPayload($data, $module)),
                'event' => $item->update($this->eventPayload($data)),
                'itinerary' => $item->update($this->itineraryPayload($data)),
            };
        });

        return redirect()
            ->route('admin.'.$module.'.index')
            ->with('status', 'Cadastro atualizado com sucesso.');
    }

    public function destroy(int $id, string $module): RedirectResponse
    {
        $this->findItem($module, $id)->delete();

        return redirect()
            ->route('admin.'.$module.'.index')
            ->with('status', 'Cadastro removido com sucesso.');
    }

    /**
     * Module configuration.
     *
     * @return array{title: string, description: string}
     */
    private function getModuleConfig(string $module): array
    {
        $configs = [
            'tourist-spots' => [
                'title' => 'Pontos Turísticos',
                'description' => 'Gerenciamento de atrativos naturais, praias, mirantes e pontos de interesse validados.',
            ],
            'culture' => [
                'title' => 'História e Cultura',
                'description' => 'Gerenciamento de patrimônios históricos, templos, ruínas e equipamentos culturais.',
            ],
            'establishments' => [
                'title' => 'Estabelecimentos e Trade Turístico',
                'description' => 'Hospedagens, restaurantes e comércios locais com Selo de Qualidade Municipal.',
            ],
            'tours' => [
                'title' => 'Passeios e Atividades',
                'description' => 'Circuitos turísticos, passeios ecológicos de barco e vivências comunitárias.',
            ],
            'guides' => [
                'title' => 'Guias Turísticos Cadastrados',
                'description' => 'Condutores e profissionais credenciados aptos para atendimento ao turista.',
            ],
            'events' => [
                'title' => 'Agenda e Festividades',
                'description' => 'Eventos oficiais, festivais gastronômicos e celebrações culturais do município.',
            ],
            'official-itineraries' => [
                'title' => 'Roteiros Oficiais',
                'description' => 'Roteiros pré-configurados pela Secretaria de Turismo para diferentes perfis de público.',
            ],
        ];

        return $configs[$module] ?? [
            'title' => 'Módulo Administrativo',
            'description' => 'Gestão de cadastros municipais.',
        ];
    }

    private function formView(string $module, mixed $item = null): View
    {
        $config = $this->getModuleConfig($module);

        return view('admin.form', [
            'module' => $module,
            'title' => $config['title'],
            'description' => $config['description'],
            'item' => $item,
            'type' => $this->modelTypeForModule($module),
            'categories' => Categoria::orderBy('nome')->get(),
        ]);
    }

    private function queryForModule(string $module)
    {
        return match ($module) {
            'tourist-spots' => Atrativo::with(['categoria', 'recursosAcessibilidade']),
            'culture' => Atrativo::whereHas('categoria', fn ($query) => $query->whereIn('slug', ['patrimonio-historico', 'cultura-e-tradicao']))->with('categoria'),
            'establishments' => Estabelecimento::whereIn('tipo_estabelecimento', ['hospedagem', 'gastronomia', 'lodging', 'gastronomy']),
            'tours' => Estabelecimento::whereIn('tipo_estabelecimento', ['atividade', 'guia_turistico', 'activity', 'tour_guide']),
            'guides' => Estabelecimento::whereIn('tipo_estabelecimento', ['guia_turistico', 'tour_guide']),
            'events' => Evento::query(),
            'official-itineraries' => Roteiro::with('itens.atrativo'),
            default => abort(404),
        };
    }

    private function findItem(string $module, int $id): mixed
    {
        return $this->queryForModule($module)->findOrFail($id);
    }

    private function modelTypeForModule(string $module): string
    {
        return match ($module) {
            'tourist-spots', 'culture' => 'place',
            'establishments', 'tours', 'guides' => 'business',
            'events' => 'event',
            'official-itineraries' => 'itinerary',
            default => abort(404),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, string $module, ?int $id = null): array
    {
        return match ($this->modelTypeForModule($module)) {
            'place' => $request->validate([
                'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
                'nome' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', 'unique:atrativos,slug,'.($id ?? 'NULL')],
                'descricao' => ['nullable', 'string'],
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
                'duracao_minutos' => ['required', 'integer', 'min:1', 'max:1440'],
                'custo_medio' => ['nullable', 'numeric', 'min:0', 'max:999999'],
                'intensidade' => ['required', 'in:low,medium,high'],
                'tags' => ['nullable', 'string'],
                'is_ar_livre' => ['nullable', 'boolean'],
                'adequado_criancas' => ['nullable', 'boolean'],
                'is_disponivel' => ['nullable', 'boolean'],
            ]),
            'business' => $request->validate([
                'nome' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', 'unique:estabelecimentos,slug,'.($id ?? 'NULL')],
                'tipo_estabelecimento' => ['nullable', 'string', 'max:80'],
                'descricao' => ['required', 'string'],
                'endereco' => ['nullable', 'string', 'max:255'],
                'bairro' => ['nullable', 'string', 'max:255'],
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
                'telefone' => ['nullable', 'string', 'max:40'],
                'whatsapp' => ['nullable', 'string', 'max:40'],
                'instagram' => ['nullable', 'string', 'max:80'],
                'website' => ['nullable', 'url', 'max:255'],
                'faixa_preco' => ['required', 'string', 'max:10'],
                'tem_selo_qualidade' => ['nullable', 'boolean'],
                'status_validacao' => ['required', 'in:pending,approved,rejected,suspended'],
            ]),
            'event' => $request->validate([
                'nome' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', 'unique:eventos,slug,'.($id ?? 'NULL')],
                'descricao' => ['required', 'string'],
                'nome_local' => ['required', 'string', 'max:255'],
                'endereco' => ['nullable', 'string', 'max:255'],
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
                'inicia_em' => ['required', 'date'],
                'termina_em' => ['nullable', 'date', 'after_or_equal:inicia_em'],
                'is_gratuito' => ['nullable', 'boolean'],
                'preco' => ['nullable', 'numeric', 'min:0', 'max:999999'],
                'is_acessivel' => ['nullable', 'boolean'],
                'categoria' => ['required', 'string', 'max:80'],
                'organizador' => ['nullable', 'string', 'max:255'],
                'capacidade' => ['nullable', 'integer', 'min:1'],
                'status' => ['required', 'in:scheduled,draft,cancelled,finished'],
            ]),
            'itinerary' => $request->validate([
                'titulo' => ['required', 'string', 'max:255'],
                'resumo' => ['required', 'string'],
                'duracao_total_minutos' => ['required', 'integer', 'min:1', 'max:1440'],
                'custo_total_estimado' => ['nullable', 'numeric', 'min:0', 'max:999999'],
                'status' => ['required', 'string', 'max:40'],
            ]),
        };
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function placePayload(array $data, string $module): array
    {
        return [
            'categoria_id' => $data['categoria_id'],
            'nome' => $data['nome'],
            'slug' => $this->slugFrom($data),
            'descricao' => $data['descricao'] ?? null,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'duracao_minutos' => $data['duracao_minutos'],
            'custo_medio' => $data['custo_medio'] ?? 0,
            'is_ar_livre' => (bool) ($data['is_ar_livre'] ?? false),
            'adequado_criancas' => (bool) ($data['adequado_criancas'] ?? false),
            'intensidade' => $data['intensidade'],
            'tags' => $this->tagsFrom($data['tags'] ?? ''),
            'is_disponivel' => (bool) ($data['is_disponivel'] ?? false),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function businessPayload(array $data, string $module): array
    {
        return [
            'nome' => $data['nome'],
            'slug' => $this->slugFrom($data),
            'tipo_estabelecimento' => $data['tipo_estabelecimento'] ?: $this->businessTypeForModule($module),
            'descricao' => $data['descricao'],
            'endereco' => $data['endereco'] ?? null,
            'bairro' => $data['bairro'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'telefone' => $data['telefone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'instagram' => $data['instagram'] ?? null,
            'website' => $data['website'] ?? null,
            'faixa_preco' => $data['faixa_preco'],
            'tem_selo_qualidade' => (bool) ($data['tem_selo_qualidade'] ?? false),
            'status_validacao' => $data['status_validacao'],
            'validado_em' => ($data['status_validacao'] ?? null) === 'approved' ? now() : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function eventPayload(array $data): array
    {
        return [
            'nome' => $data['nome'],
            'slug' => $this->slugFrom($data),
            'descricao' => $data['descricao'],
            'nome_local' => $data['nome_local'],
            'endereco' => $data['endereco'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'inicia_em' => $data['inicia_em'],
            'termina_em' => $data['termina_em'] ?? null,
            'is_gratuito' => (bool) ($data['is_gratuito'] ?? false),
            'preco' => $data['preco'] ?? null,
            'is_acessivel' => (bool) ($data['is_acessivel'] ?? false),
            'categoria' => $data['categoria'],
            'organizador' => $data['organizador'] ?? null,
            'capacidade' => $data['capacidade'] ?? null,
            'status' => $data['status'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function itineraryPayload(array $data): array
    {
        return [
            'titulo' => $data['titulo'],
            'resumo' => $data['resumo'],
            'duracao_total_minutos' => $data['duracao_total_minutos'],
            'custo_total_estimado' => $data['custo_total_estimado'] ?? 0,
            'status' => $data['status'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createOfficialItinerary(array $data): void
    {
        $preference = PreferenciaVisitante::create([
            'descricao_original' => 'Roteiro oficial criado pela gestão municipal.',
            'humores' => [],
            'interesses' => [],
            'minutos_disponiveis' => $data['duracao_total_minutos'],
            'orcamento' => $data['custo_total_estimado'] ?? 0,
            'tem_criancas' => null,
            'transporte' => null,
            'requisitos_acessibilidade' => [],
            'intensidade' => null,
        ]);

        Roteiro::create([
            ...$this->itineraryPayload($data),
            'preferencia_visitante_id' => $preference->id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function slugFrom(array $data): string
    {
        return filled($data['slug'] ?? null)
            ? Str::slug((string) $data['slug'])
            : Str::slug((string) $data['nome']);
    }

    /**
     * @return array<int, string>
     */
    private function tagsFrom(string $tags): array
    {
        return collect(explode(',', $tags))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->values()
            ->all();
    }

    private function businessTypeForModule(string $module): string
    {
        return match ($module) {
            'guides' => 'guia_turistico',
            'tours' => 'atividade',
            default => 'gastronomia',
        };
    }

    private function publicRouteForModule(string $module): ?string
    {
        return match ($module) {
            'tourist-spots' => 'tourist-spots.show',
            'culture' => 'culture.show',
            'events' => 'agenda.show',
            'tours' => 'tours.show',
            'guides' => 'guides.show',
            'establishments' => 'dining.show',
            default => null,
        };
    }
}
