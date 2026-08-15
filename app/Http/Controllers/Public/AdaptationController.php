<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Itinerary;
use App\Models\RouteAdaptation;
use App\Services\Adaptation\RouteAdaptationService;
use App\Services\Ai\AiAdaptationWriter;
use Illuminate\Support\Facades\DB;

class AdaptationController extends Controller
{
    public function rain(
        Itinerary $itinerary,
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
                ) {
                    $adaptation =
                        RouteAdaptation::create([
                            'itinerary_id' => $itinerary->id,

                            'event' => 'RAIN_STARTED',

                            'title' => $narrative->title,

                            'summary' => $narrative->summary,

                            'total_duration_minutes' => $adapted
                                ->totalDurationMinutes,

                            'total_estimated_cost' => $adapted
                                ->totalEstimatedCost,
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
                            ->items()
                            ->create([
                                'place_id' => $stop->placeId,

                                'position' => $position + 1,

                                'action' => $action,

                                'duration_minutes' => $stop
                                    ->durationMinutes,

                                'estimated_cost' => $stop
                                    ->estimatedCost,

                                'reason' => $this->findReason(
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
                                ->items
                                ->firstWhere(
                                    'place_id',
                                    $removedId
                                );

                        if (! $original) {
                            continue;
                        }

                        $adaptation
                            ->items()
                            ->create([
                                'place_id' => $removedId,

                                'position' => $original
                                    ->position,

                                'action' => 'REMOVED',

                                'duration_minutes' => $original
                                    ->duration_minutes,

                                'estimated_cost' => $original
                                    ->estimated_cost,

                                'reason' => 'Removido porque a atividade é externa e começou a chover.',
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
        Itinerary $itinerary,
        RouteAdaptation $adaptation
    ) {
        $itinerary->load([
            'items.place.category',
        ]);

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
