<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Atrativo;
use Illuminate\Contracts\View\View;

class CityMapController extends Controller
{
    /**
     * Display the full interactive city map with official places.
     */
    public function __invoke(): View
    {
        $places = Atrativo::with(['categoria', 'recursosAcessibilidade'])
            ->where('is_disponivel', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $mapStops = $places->map(function (Atrativo $place): array {
            return [
                'id' => $place->id,
                'name' => $place->nome,
                'category' => $place->categoria->nome ?? $place->category->name ?? 'Atrativo',
                'latitude' => (float) $place->latitude,
                'longitude' => (float) $place->longitude,
                'duration' => $place->duracao_minutos,
                'cost' => (float) $place->custo_medio,
                'slug' => $place->slug,
            ];
        });

        return view('pages.map', [
            'places' => $places,
            'mapStops' => $mapStops,
        ]);
    }
}
