<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Event;
use App\Models\Itinerary;
use App\Models\Place;
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
            'tourist-spots' => Place::with(['category', 'accessibilityFeatures', 'schedules'])
                ->where('is_available', true)
                ->get(),

            'culture' => Place::whereHas('category', function ($query): void {
                $query->whereIn('slug', ['patrimonio-historico', 'cultura-e-tradicao']);
            })
                ->with(['category', 'accessibilityFeatures'])
                ->get(),

            'tours' => Business::whereIn('business_type', ['activity', 'tour_guide'])
                ->where('validation_status', 'approved')
                ->get(),

            'guides' => Business::where('business_type', 'tour_guide')
                ->where('validation_status', 'approved')
                ->get(),

            'official-itineraries' => Itinerary::where('status', 'official')
                ->orWhere('title', 'like', 'Roteiro%')
                ->with(['items.place.category'])
                ->get(),

            'stays' => Business::where('business_type', 'lodging')
                ->where('validation_status', 'approved')
                ->get(),

            'dining' => Business::where('business_type', 'gastronomy')
                ->where('validation_status', 'approved')
                ->get(),

            'agenda' => Event::where('status', 'scheduled')
                ->orderBy('starts_at')
                ->get(),

            default => Place::all(),
        };
    }

    /**
     * Query single item by section and slug.
     */
    private function fetchSingleItem(string $section, string $slug): mixed
    {
        return match ($section) {
            'tourist-spots', 'culture' => Place::where('slug', $slug)
                ->with(['category', 'accessibilityFeatures', 'schedules'])
                ->first(),

            'stays', 'dining', 'guides', 'tours' => Business::where('slug', $slug)
                ->first(),

            'agenda' => Event::where('slug', $slug)
                ->first(),

            'official-itineraries' => Itinerary::where('title', 'like', '%'.str_replace('-', ' ', $slug).'%')
                ->orWhere('id', is_numeric($slug) ? (int) $slug : 0)
                ->with(['items.place.category'])
                ->first() ?? Itinerary::with(['items.place.category'])->first(),

            default => Place::where('slug', $slug)->first(),
        };
    }
}
