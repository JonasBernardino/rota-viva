<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdaptacaoRota;
use App\Models\Roteiro;
use App\Services\Adaptation\RouteAdaptationService;
use App\Services\Ai\AiAdaptationWriter;
use App\Services\Analytics\JourneyAnalyticsService;
use Illuminate\Support\Facades\DB;

class AdaptationController extends Controller
{
    public function rain(
        Roteiro $itinerary,
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
        | A IA não escolhe os novos atrativos.
        |
        | Ela recebe a rota já recalculada pelo backend
        | e apenas produz título, resumo e explicações.
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
        | Tudo fica dentro de uma transaction para não termos
        | uma AdaptacaoRota salva pela metade.
        |
        */
        $adaptation = DB::transaction(
            function () use (
                $itinerary,
                $adapted,
                $narrative
            ): AdaptacaoRota {
                /** @var AdaptacaoRota $adaptation */
                $adaptation = AdaptacaoRota::create([
                    'roteiro_id' =>
                        $itinerary->id,

                    'evento' =>
                        'RAIN_STARTED',

                    'titulo' =>
                        $narrative->title,

                    'resumo' =>
                        $narrative->summary,

                    'duracao_total_minutos' =>
                        $adapted->totalDurationMinutes,

                    'custo_total_estimado' =>
                        $adapted->totalEstimatedCost,
                ]);

                /*
                |--------------------------------------------------------------------------
                | 3.1 Itens mantidos ou adicionados
                |--------------------------------------------------------------------------
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
                        ->itens()
                        ->create([
                            'atrativo_id' =>
                                $stop->placeId,

                            'posicao' =>
                                $position + 1,

                            'acao' =>
                                $action,

                            'duracao_minutos' =>
                                $stop->durationMinutes,

                            'custo_estimado' =>
                                $stop->estimatedCost,

                            'motivo' =>
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
                | Também persistimos os atrativos removidos.
                |
                | Isso continua sendo importante para:
                |
                | - comparação antes/depois;
                | - auditoria;
                | - analytics;
                | - mapa de calor de atrativos removidos.
                |
                */
                foreach (
                    $adapted->removedPlaceIds
                    as $removedId
                ) {
                    $original = $itinerary
                        ->itens
                        ->firstWhere(
                            'atrativo_id',
                            $removedId
                        );

                    if (!$original) {
                        continue;
                    }

                    $adaptation
                        ->itens()
                        ->create([
                            'atrativo_id' =>
                                $removedId,

                            'posicao' =>
                                $original->posicao,

                            'acao' =>
                                'REMOVED',

                            'duracao_minutos' =>
                                $original->duracao_minutos,

                            'custo_estimado' =>
                                $original->custo_estimado,

                            'motivo' =>
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
        | 4. Analytics
        |--------------------------------------------------------------------------
        |
        | Registramos a adaptação depois da transaction principal.
        |
        | Se analytics falhar, o JourneyAnalyticsService trata
        | internamente e não prejudica a jornada do visitante.
        |
        */
        $analytics->trackRouteAdapted(
            adaptation: $adaptation,
            adapted: $adapted,
        );

        /*
        |--------------------------------------------------------------------------
        | 5. Redireciona para antes/depois
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
        Roteiro $itinerary,
        AdaptacaoRota $adaptation
    ) {
        /*
        |--------------------------------------------------------------------------
        | Garante vínculo entre roteiro e adaptação
        |--------------------------------------------------------------------------
        */
        abort_unless(
            $adaptation->roteiro_id
                === $itinerary->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Carrega roteiro original
        |--------------------------------------------------------------------------
        */
        $itinerary->load([
            'preferencia',
            'itens.atrativo.categoria',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Carrega adaptação
        |--------------------------------------------------------------------------
        */
        $adaptation->load([
            'itens.atrativo.categoria',
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