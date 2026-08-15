<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Atrativo;
use App\Services\Tenant\TenantManager;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(TenantManager $tenantManager): View
    {
        $municipality = $tenantManager->current();

        return view('home', [
            'municipality' => $municipality,
            'homeContent' => $this->homeContent($municipality),
            'featuredPlaces' => $this->featuredPlaces($municipality !== null),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function homeContent(mixed $municipality): array
    {
        return [
            'brand_name' => $municipality?->brandName() ?: 'ROTA VIVA',
            'brand_logo_url' => $municipality?->brandLogoUrl(),
            'hero_eyebrow' => $municipality?->hero_eyebrow ?: 'Turismo inteligente',
            'hero_title' => $municipality?->hero_title ?: 'Como você quer viver a cidade hoje?',
            'hero_description' => $municipality?->hero_description
                ?: 'Conte o que você procura e receba uma experiência que se adapta ao seu tempo, orçamento e interesses.',
            'hero_image_url' => $municipality?->heroImageUrl() ?: asset('images/rota-viva-hero.webp'),
            'hero_image_alt' => $municipality?->hero_image_alt
                ?: 'Cidade histórica à beira-mar, cercada por montanhas e natureza',
            'hero_search_placeholder' => $municipality?->hero_search_placeholder
                ?: 'Ex.: Quero cultura e tranquilidade, tenho 4 horas...',
            'hero_card_title' => $municipality?->hero_card_title ?: 'Sua experiência, em movimento',
            'hero_card_tags' => $municipality?->hero_card_tags ?: ['4 horas', 'Família', 'Cultura'],
            'local_economy_eyebrow' => $municipality?->local_economy_eyebrow ?: 'Economia local',
            'local_economy_title' => $municipality?->local_economy_title ?: 'Cada rota também movimenta o território',
            'local_economy_description' => $municipality?->local_economy_description
                ?: 'Pequenos negócios, experiências culturais e lugares menos conhecidos passam a fazer parte do percurso de forma relevante — nunca como publicidade invasiva.',
            'local_economy_stat' => $municipality?->local_economy_stat ?: '+ oportunidades locais no caminho',
            'local_economy_link_label' => $municipality?->local_economy_link_label ?: 'Conheça quem faz a cidade',
            'local_economy_link_url' => $municipality?->local_economy_link_url ?: route('guides.index'),
            'local_economy_image_url' => $municipality?->localEconomyImageUrl() ?: asset('images/local-artisan.webp'),
            'local_economy_image_alt' => $municipality?->local_economy_image_alt
                ?: 'Artesã local sorrindo enquanto modela uma peça de cerâmica',
        ];
    }

    private function featuredPlaces(bool $hasMunicipality): Collection
    {
        if (! $hasMunicipality) {
            return collect();
        }

        return Atrativo::query()
            ->with(['categoria', 'midias'])
            ->where('is_disponivel', true)
            ->orderByDesc('adequado_criancas')
            ->orderBy('nome')
            ->take(3)
            ->get()
            ->values();
    }
}
