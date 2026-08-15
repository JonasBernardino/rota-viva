<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Atrativo;
use App\Models\Estabelecimento;
use App\Models\Evento;
use App\Models\Roteiro;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display a listing of items for the given catalog section.
     */
    public function index(Request $request): View
    {
        $section = $request->route('section', 'tourist-spots');
        $config = $this->getSectionConfig($section);

        $items = $this->fetchItemsForSection($section);

        return view('pages.catalog', [
            'section' => $section,
            'title' => $config['title'],
            'eyebrow' => $config['eyebrow'],
            'description' => $config['description'],
            'routePrefix' => $section,
            'items' => $items,
        ]);
    }

    /**
     * Display details for a specific item in the catalog.
     */
    public function show(Request $request, string $slug): View
    {
        $section = $request->route('section', 'tourist-spots');
        $config = $this->getSectionConfig($section);

        $item = $this->fetchSingleItem($section, $slug);
        abort_if(! $item, 404);

        return view('pages.detail', [
            'section' => $section,
            'catalogTitle' => $config['title'],
            'catalogDescription' => $config['description'],
            'routePrefix' => $section,
            'slug' => $slug,
            'item' => $item,
        ]);
    }

    /**
     * Get section configuration metadata.
     *
     * @return array{title: string, eyebrow: string, description: string}
     */
    private function getSectionConfig(string $section): array
    {
        $configs = [
            'tourist-spots' => [
                'title' => 'Pontos turísticos',
                'eyebrow' => 'Atrativos oficiais',
                'description' => 'Lugares que revelam a natureza, a história e a identidade do município de Lucena com informações validadas.',
            ],
            'culture' => [
                'title' => 'História e cultura',
                'eyebrow' => 'Identidade territorial',
                'description' => 'Histórias, tradições, patrimônios barrocos e vozes que mantêm viva a memória secular de Lucena.',
            ],
            'tours' => [
                'title' => 'Passeios e experiências',
                'eyebrow' => 'Atividades locais',
                'description' => 'Passeios ecológicos de barco pelo Rio Miriri, oficinas de renda com mestras artesãs e circuitos guiados.',
            ],
            'guides' => [
                'title' => 'Guias turísticos',
                'eyebrow' => 'Profissionais validados',
                'description' => 'Encontre condutores e guias credenciados com Selo de Qualidade Municipal para uma visita segura e enriquecedora.',
            ],
            'official-itineraries' => [
                'title' => 'Roteiros oficiais',
                'eyebrow' => 'Curadoria municipal',
                'description' => 'Percursos oficiais temáticos e equilibrados organizados pela Secretaria Municipal de Turismo.',
            ],
            'stays' => [
                'title' => 'Onde ficar',
                'eyebrow' => 'Hospedagens',
                'description' => 'Pousadas à beira-mar, chalés ecológicos e hospedagens familiares cadastradas e verificadas.',
            ],
            'dining' => [
                'title' => 'Onde comer',
                'eyebrow' => 'Gastronomia local',
                'description' => 'Restaurantes de peixada paraibana, quiosques com peixe na telha e cafés com tapiocas artesanais.',
            ],
            'agenda' => [
                'title' => 'Agenda',
                'eyebrow' => 'Acontece na cidade',
                'description' => 'Festas tradicionais, festivais gastronômicos, rodas de coco de roda e eventos culturais com data marcada.',
            ],
        ];

        return $configs[$section] ?? [
            'title' => 'Catálogo',
            'eyebrow' => 'Exploração',
            'description' => 'Conheça tudo o que Lucena tem a oferecer.',
        ];
    }

    /**
     * Query real database records for each catalog section.
     */
    private function fetchItemsForSection(string $section)
    {
        return match ($section) {
            'tourist-spots' => Atrativo::with(['categoria', 'recursosAcessibilidade', 'horarios'])
                ->where('is_disponivel', true)
                ->get(),

            'culture' => Atrativo::whereHas('categoria', function ($query): void {
                $query->whereIn('slug', ['patrimonio-historico', 'cultura-e-tradicao']);
            })
                ->with(['categoria', 'recursosAcessibilidade'])
                ->get(),

            'tours' => Estabelecimento::whereIn('tipo_estabelecimento', ['atividade', 'guia_turistico', 'activity', 'tour_guide'])
                ->where('status_validacao', 'approved')
                ->get(),

            'guides' => Estabelecimento::whereIn('tipo_estabelecimento', ['guia_turistico', 'tour_guide'])
                ->where('status_validacao', 'approved')
                ->get(),

            'official-itineraries' => Roteiro::where('status', 'official')
                ->orWhere('titulo', 'like', 'Roteiro%')
                ->with(['itens.atrativo.categoria'])
                ->get(),

            'stays' => Estabelecimento::whereIn('tipo_estabelecimento', ['hospedagem', 'lodging'])
                ->where('status_validacao', 'approved')
                ->get(),

            'dining' => Estabelecimento::whereIn('tipo_estabelecimento', ['gastronomia', 'gastronomy'])
                ->where('status_validacao', 'approved')
                ->get(),

            'agenda' => Evento::where('status', 'scheduled')
                ->orderBy('inicia_em')
                ->get(),

            default => Atrativo::all(),
        };
    }

    /**
     * Query single item by section and slug.
     */
    private function fetchSingleItem(string $section, string $slug): mixed
    {
        return match ($section) {
            'tourist-spots', 'culture' => Atrativo::where('slug', $slug)
                ->with(['categoria', 'recursosAcessibilidade', 'horarios'])
                ->first(),

            'stays', 'dining', 'guides', 'tours' => Estabelecimento::where('slug', $slug)
                ->first(),

            'agenda' => Evento::where('slug', $slug)
                ->first(),

            'official-itineraries' => Roteiro::where('titulo', 'like', '%'.str_replace('-', ' ', $slug).'%')
                ->orWhere('id', is_numeric($slug) ? (int) $slug : 0)
                ->with(['itens.atrativo.categoria'])
                ->first() ?? Roteiro::with(['itens.atrativo.categoria'])->first(),

            default => Atrativo::where('slug', $slug)->first(),
        };
    }
}
