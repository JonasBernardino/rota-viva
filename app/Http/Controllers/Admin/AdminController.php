<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Atrativo;
use App\Models\Estabelecimento;
use App\Models\Evento;
use App\Models\Roteiro;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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

        $items = match ($module) {
            'tourist-spots' => Atrativo::with(['categoria', 'recursosAcessibilidade'])->latest()->get(),
            'culture' => Atrativo::whereHas('categoria', fn ($q) => $q->whereIn('slug', ['patrimonio-historico', 'cultura-e-tradicao']))->with('categoria')->latest()->get(),
            'establishments' => Estabelecimento::whereIn('tipo_estabelecimento', ['hospedagem', 'gastronomia', 'lodging', 'gastronomy'])->latest()->get(),
            'tours' => Estabelecimento::whereIn('tipo_estabelecimento', ['atividade', 'guia_turistico', 'activity', 'tour_guide'])->latest()->get(),
            'guides' => Estabelecimento::whereIn('tipo_estabelecimento', ['guia_turistico', 'tour_guide'])->latest()->get(),
            'events' => Evento::latest()->get(),
            'official-itineraries' => Roteiro::with('itens.atrativo')->latest()->get(),
            default => Atrativo::latest()->get(),
        };

        return view('admin.module', [
            'module' => $module,
            'title' => $config['title'],
            'description' => $config['description'],
            'items' => $items,
        ]);
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
}
