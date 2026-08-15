<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdaptacaoRota;
use App\Models\Roteiro;
use App\Services\Adaptation\RouteAdaptationService;
use App\Services\Ai\AiAdaptationWriter;
use Illuminate\Support\Facades\DB;

class AdaptationController extends Controller
{
    public function rain(
        Roteiro $itinerary,
        RouteAdaptationService $adaptationService,
        AiAdaptationWriter $writer
    ) {
        $adapted =
            $adaptationService
                ->adaptForRain($itinerary);

        $narrative =
            $writer
                ->explainRainAdaptation(
                    $itinerary,
                    $adapted
                );

        $adaptation =
            DB::transaction(
                function () use (
                    $itinerary,
                    $adapted,
                    $narrative
                ): AdaptacaoRota {
                    /** @var AdaptacaoRota $adaptation */
                    $adaptation =
                        AdaptacaoRota::create([
                            'roteiro_id' => $itinerary->id,
                            'evento' => 'RAIN_STARTED',
                            'titulo' => $narrative->title,
                            'resumo' => $narrative->summary,
                            'duracao_total_minutos' => $adapted->totalDurationMinutes,
                            'custo_total_estimado' => $adapted->totalEstimatedCost,
                        ]);

                    foreach (
                        $adapted->stops as $position => $stop
                    ) {
                        $action =
                            in_array(
                                $stop->placeId,
                                $adapted->addedPlaceIds,
                                true
                            )
                                ? 'ADDED'
                                : 'KEPT';

                        $adaptation
                            ->itens()
                            ->create([
                                'atrativo_id' => $stop->placeId,
                                'posicao' => $position + 1,
                                'acao' => $action,
                                'duracao_minutos' => $stop->durationMinutes,
                                'custo_estimado' => $stop->estimatedCost,
                                'motivo' => $this->findReason(
                                    $narrative->changes,
                                    $stop->placeId
                                ),
                            ]);
                    }

                    foreach (
                        $adapted->removedPlaceIds as $removedId
                    ) {
                        $original =
                            $itinerary
                                ->itens
                                ->firstWhere(
                                    'atrativo_id',
                                    $removedId
                                );

                        if (! $original) {
                            continue;
                        }

                        $adaptation
                            ->itens()
                            ->create([
                                'atrativo_id' => $removedId,
                                'posicao' => $original->posicao,
                                'acao' => 'REMOVED',
                                'duracao_minutos' => $original->duracao_minutos,
                                'custo_estimado' => $original->custo_estimado,
                                'motivo' => 'Removido porque a atividade é externa e começou a chover.',
                            ]);
                    }

                    return $adaptation;
                }
            );

        return redirect()->route(
            'routes.adaptation.show',
            [
                'itinerary' => $itinerary,
                'adaptation' => $adaptation,
            ]
        );
    }

    public function show(
        Roteiro $itinerary,
        AdaptacaoRota $adaptation
    ) {
        $itinerary->load([
            'itens.atrativo.categoria',
        ]);

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
        int $placeId
    ): string {
        foreach ($changes as $change) {
            if (
                ($change['place_id'] ?? null)
                === $placeId
            ) {
                return $change['message'];
            }
        }

        return 'Mantido por continuar compatível com sua rota.';
    }
}
