<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Itinerary;
use App\Models\RouteAdaptation;
use App\Services\Adaptation\RouteAdaptationService;
use App\Services\Ai\AiAdaptationWriter;
use App\Services\Analytics\JourneyAnalyticsService;
use Illuminate\Support\Facades\DB;

class AdaptationController extends Controller
{
    public function rain(
        Itinerary $itinerary,
        RouteAdaptationService $adaptationService,
        AiAdaptationWriter $writer,
        JourneyAnalyticsService $analytics,
    ) {
        /*
        |--------------------------------------------------------------------------
        | 1. Gera a nova rota adaptada
        |--------------------------------------------------------------------------
        |
        | O backend identifica quais pontos da rota original
        | são incompatíveis com chuva e procura substitutos.
        |
        */
        $adapted = $adaptationService->adaptForRain(
            $itinerary
        );

        /*
        |--------------------------------------------------------------------------
        | 2. IA explica a adaptação
        |--------------------------------------------------------------------------
        |
        | A IA NÃO escolhe os novos lugares.
        |
        | Ela recebe a rota já recalculada pelo backend
        | e apenas produz título, resumo e explicações.
        |
        | O AiAdaptationWriter já possui fallback caso
        | o provider de IA esteja indisponível.
        |
        */
        $narrative = $writer->explainRainAdaptation(
            $itinerary,
            $adapted
        );

        /*
        |--------------------------------------------------------------------------
        | 3. Salva adaptação + itens
        |--------------------------------------------------------------------------
        |
        | Tudo dentro de uma transaction para não termos
        | uma RouteAdaptation salva pela metade.
        |
        */
        $adaptation = DB::transaction(
            function () use (
                $itinerary,
                $adapted,
                $narrative
            ) {
                $adaptation = RouteAdaptation::create([
                    'itinerary_id' =>
                        $itinerary->id,

                    'event' =>
                        'RAIN_STARTED',

                    'title' =>
                        $narrative->title,

                    'summary' =>
                        $narrative->summary,

                    'total_duration_minutes' =>
                        $adapted->totalDurationMinutes,

                    'total_estimated_cost' =>
                        $adapted->totalEstimatedCost,
                ]);

                /*
                |--------------------------------------------------------------------------
                | 3.1 Itens que continuam na nova rota
                |--------------------------------------------------------------------------
                |
                | Aqui entram:
                |
                | KEPT  = já existia e permaneceu.
                | ADDED = entrou como substituição.
                |
                */
                foreach (
                    $adapted->stops
                    as $position => $stop
                ) {
                    $action = in_array(
                        $stop->placeId,
                        $adapted->addedPlaceIds,
                        true
                    )
                        ? 'ADDED'
                        : 'KEPT';

                    $adaptation
                        ->items()
                        ->create([
                            'place_id' =>
                                $stop->placeId,

                            'position' =>
                                $position + 1,

                            'action' =>
                                $action,

                            'duration_minutes' =>
                                $stop->durationMinutes,

                            'estimated_cost' =>
                                $stop->estimatedCost,

                            'reason' =>
                                $this->findReason(
                                    $narrative->changes,
                                    $stop->placeId
                                ),
                        ]);
                }

                /*
                |--------------------------------------------------------------------------
                | 3.2 Itens removidos
                |--------------------------------------------------------------------------
                |
                | Também persistimos os pontos removidos.
                |
                | Isso é importante para:
                |
                | - comparação antes/depois;
                | - auditoria;
                | - analytics;
                | - mapa de calor de pontos removidos.
                |
                */
                foreach (
                    $adapted->removedPlaceIds
                    as $removedId
                ) {
                    $originalItem = $itinerary
                        ->items
                        ->firstWhere(
                            'place_id',
                            $removedId
                        );

                    if (!$originalItem) {
                        continue;
                    }

                    $adaptation
                        ->items()
                        ->create([
                            'place_id' =>
                                $removedId,

                            'position' =>
                                $originalItem->position,

                            'action' =>
                                'REMOVED',

                            'duration_minutes' =>
                                $originalItem
                                    ->duration_minutes,

                            'estimated_cost' =>
                                $originalItem
                                    ->estimated_cost,

                            'reason' =>
                                $this->findReason(
                                    $narrative->changes,
                                    $removedId,
                                    'Removido porque a atividade é externa e começou a chover.'
                                ),
                        ]);
                }

                return $adaptation;
            }
        );

        /*
        |--------------------------------------------------------------------------
        | 4. Registra analytics
        |--------------------------------------------------------------------------
        |
        | Importante:
        |
        | analytics acontece DEPOIS da transaction principal.
        |
        | Se analytics falhar, JourneyAnalyticsService captura
        | internamente o erro e a adaptação continua funcionando.
        |
        */
        $analytics->trackRouteAdapted(
            adaptation: $adaptation,
            adapted: $adapted,
        );

        /*
        |--------------------------------------------------------------------------
        | 5. Redireciona para comparação antes/depois
        |--------------------------------------------------------------------------
        */
        return redirect()->route(
            'routes.adaptation.show',
            [
                'itinerary' =>
                    $itinerary,

                'adaptation' =>
                    $adaptation,
            ]
        );
    }

    public function show(
        Itinerary $itinerary,
        RouteAdaptation $adaptation
    ) {
        /*
        |--------------------------------------------------------------------------
        | Segurança básica de vínculo
        |--------------------------------------------------------------------------
        |
        | Evita alguém acessar algo como:
        |
        | /minha-rota/1/adaptacoes/99
        |
        | quando a adaptação 99 pertence a outra rota.
        |
        */
        abort_unless(
            $adaptation->itinerary_id
                === $itinerary->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Carrega rota original
        |--------------------------------------------------------------------------
        */
        $itinerary->load([
            'preference',
            'items.place.category',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Carrega adaptação
        |--------------------------------------------------------------------------
        */
        $adaptation->load([
            'items.place.category',
        ]);

        return view(
            'pages.route-adaptation',
            compact(
                'itinerary',
                'adaptation'
            )
        );
    }

    private function findReason(
        array $changes,
        int $placeId,
        string $fallback = 'Mantido por continuar compatível com sua rota.'
    ): string {
        foreach ($changes as $change) {
            if (
                (int) (
                    $change['place_id']
                    ?? 0
                ) === $placeId
            ) {
                return $change['message']
                    ?? $fallback;
            }
        }

        return $fallback;
    }
}