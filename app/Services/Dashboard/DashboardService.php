<?php

namespace App\Services\Dashboard;

use App\Enums\JourneyEventType;
use App\Models\AdaptacaoRota;
use App\Models\JourneyEvent;
use App\Models\Roteiro;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function build(
        string $period
    ): array {
        $from = $this->resolveStartDate(
            $period
        );

        return [
            'overview' =>
                $this->overview($from),

            'interests' =>
                $this->topInterests($from),

            'moods' =>
                $this->topMoods($from),

            'budgets' =>
                $this->budgetDistribution($from),

            'durations' =>
                $this->durationDistribution($from),

            'topPlaces' =>
                $this->topPlaces($from),

            'unmetDemand' =>
                $this->unmetDemand($from),

            'heatmap' => [
                'demand' =>
                    $this->routeDemandHeatmap($from),

                'added' =>
                    $this->adaptationHeatmap(
                        $from,
                        'ADDED'
                    ),

                'removed' =>
                    $this->adaptationHeatmap(
                        $from,
                        'REMOVED'
                    ),
            ],
        ];
    }

    private function overview(
        ?Carbon $from
    ): array {
        $itineraries =
            Roteiro::query()
                ->when(
                    $from,
                    fn ($query) =>
                        $query->where(
                            'created_at',
                            '>=',
                            $from
                        )
                );

        $adaptations =
            AdaptacaoRota::query()
                ->when(
                    $from,
                    fn ($query) =>
                        $query->where(
                            'created_at',
                            '>=',
                            $from
                        )
                );

        $failed =
            JourneyEvent::query()
                ->where(
                    'event_type',
                    JourneyEventType::ROUTE_NOT_FOUND->value
                )
                ->when(
                    $from,
                    fn ($query) =>
                        $query->where(
                            'occurred_at',
                            '>=',
                            $from
                        )
                )
                ->count();

        $created =
            (clone $itineraries)
                ->count();

        $adapted =
            (clone $adaptations)
                ->count();

        return [
            'routesCreated' =>
                $created,

            'routesNotFound' =>
                $failed,

            'adaptations' =>
                $adapted,

            'averageCost' =>
                round(
                    (float) (
                        (clone $itineraries)
                            ->avg(
                                'custo_total_estimado'
                            ) ?? 0
                    ),
                    2
                ),

            'averageDuration' =>
                (int) round(
                    (float) (
                        (clone $itineraries)
                            ->avg(
                                'duracao_total_minutos'
                            ) ?? 0
                    )
                ),

            'adaptationRate' =>
                $created > 0
                    ? round(
                        ($adapted / $created) * 100,
                        1
                    )
                    : 0,
        ];
    }

    private function requestedEvents(
        ?Carbon $from
    ): Collection {
        return JourneyEvent::query()
            ->where(
                'event_type',
                JourneyEventType::ROUTE_REQUESTED->value
            )
            ->when(
                $from,
                fn ($query) =>
                    $query->where(
                        'occurred_at',
                        '>=',
                        $from
                    )
            )
            ->get();
    }

    private function topInterests(
        ?Carbon $from
    ): array {
        $values =
            $this->requestedEvents($from)
                ->flatMap(
                    fn (JourneyEvent $event) =>
                        data_get(
                            $event->payload,
                            'preferences.interests',
                            []
                        )
                )
                ->filter();

        return $this->ranking(
            $values
        );
    }

    private function topMoods(
        ?Carbon $from
    ): array {
        $values =
            $this->requestedEvents($from)
                ->flatMap(
                    fn (JourneyEvent $event) =>
                        data_get(
                            $event->payload,
                            'preferences.moods',
                            []
                        )
                )
                ->filter();

        return $this->ranking(
            $values
        );
    }

    private function ranking(
        Collection $values
    ): array {
        $total =
            $values->count();

        if ($total === 0) {
            return [];
        }

        return $values
            ->map(
                fn ($value) =>
                    mb_strtolower(
                        trim($value)
                    )
            )
            ->countBy()
            ->sortDesc()
            ->take(6)
            ->map(
                fn ($count, $label) => [
                    'label' =>
                        ucfirst($label),

                    'count' =>
                        $count,

                    'percentage' =>
                        round(
                            ($count / $total) * 100,
                            1
                        ),
                ]
            )
            ->values()
            ->all();
    }

    private function budgetDistribution(
        ?Carbon $from
    ): array {
        $buckets = [
            'Até R$ 50' => 0,
            'R$ 51–150' => 0,
            'R$ 151–300' => 0,
            'Acima de R$ 300' => 0,
            'Não informado' => 0,
        ];

        foreach (
            $this->requestedEvents($from)
            as $event
        ) {
            $budget =
                data_get(
                    $event->payload,
                    'preferences.budget'
                );

            if ($budget === null) {
                $buckets[
                    'Não informado'
                ]++;

                continue;
            }

            if ($budget <= 50) {
                $buckets[
                    'Até R$ 50'
                ]++;
            } elseif ($budget <= 150) {
                $buckets[
                    'R$ 51–150'
                ]++;
            } elseif ($budget <= 300) {
                $buckets[
                    'R$ 151–300'
                ]++;
            } else {
                $buckets[
                    'Acima de R$ 300'
                ]++;
            }
        }

        return $this->bucketResult(
            $buckets
        );
    }

    private function durationDistribution(
        ?Carbon $from
    ): array {
        $buckets = [
            'Até 2h' => 0,
            '2h–4h' => 0,
            '4h–6h' => 0,
            'Mais de 6h' => 0,
            'Não informado' => 0,
        ];

        foreach (
            $this->requestedEvents($from)
            as $event
        ) {
            $minutes =
                data_get(
                    $event->payload,
                    'preferences.available_minutes'
                );

            if ($minutes === null) {
                $buckets[
                    'Não informado'
                ]++;

                continue;
            }

            if ($minutes <= 120) {
                $buckets[
                    'Até 2h'
                ]++;
            } elseif ($minutes <= 240) {
                $buckets[
                    '2h–4h'
                ]++;
            } elseif ($minutes <= 360) {
                $buckets[
                    '4h–6h'
                ]++;
            } else {
                $buckets[
                    'Mais de 6h'
                ]++;
            }
        }

        return $this->bucketResult(
            $buckets
        );
    }

    private function bucketResult(
        array $buckets
    ): array {
        $total =
            array_sum(
                $buckets
            );

        return collect(
            $buckets
        )
            ->map(
                fn ($count, $label) => [
                    'label' =>
                        $label,

                    'count' =>
                        $count,

                    'percentage' =>
                        $total > 0
                            ? round(
                                ($count / $total) * 100,
                                1
                            )
                            : 0,
                ]
            )
            ->values()
            ->all();
    }

    private function topPlaces(
        ?Carbon $from
    ): array {
        return DB::table(
            'itens_roteiro as item'
        )
            ->join(
                'roteiros as roteiro',
                'roteiro.id',
                '=',
                'item.roteiro_id'
            )
            ->join(
                'atrativos as atrativo',
                'atrativo.id',
                '=',
                'item.atrativo_id'
            )
            ->when(
                $from,
                fn ($query) =>
                    $query->where(
                        'roteiro.created_at',
                        '>=',
                        $from
                    )
            )
            ->select(
                'atrativo.id',
                'atrativo.nome',

                DB::raw(
                    'COUNT(*) AS routes_count'
                )
            )
            ->groupBy(
                'atrativo.id',
                'atrativo.nome'
            )
            ->orderByDesc(
                'routes_count'
            )
            ->limit(8)
            ->get()
            ->map(
                fn ($item) => [
                    'id' =>
                        $item->id,

                    'name' =>
                        $item->nome,

                    'count' =>
                        (int)
                        $item->routes_count,
                ]
            )
            ->all();
    }

    private function routeDemandHeatmap(
        ?Carbon $from
    ): array {
        return DB::table(
            'itens_roteiro as item'
        )
            ->join(
                'roteiros as roteiro',
                'roteiro.id',
                '=',
                'item.roteiro_id'
            )
            ->join(
                'atrativos as atrativo',
                'atrativo.id',
                '=',
                'item.atrativo_id'
            )
            ->whereNotNull(
                'atrativo.latitude'
            )
            ->whereNotNull(
                'atrativo.longitude'
            )
            ->when(
                $from,
                fn ($query) =>
                    $query->where(
                        'roteiro.created_at',
                        '>=',
                        $from
                    )
            )
            ->select(
                'atrativo.id',
                'atrativo.nome',
                'atrativo.latitude',
                'atrativo.longitude',

                DB::raw(
                    'COUNT(*) AS intensity'
                )
            )
            ->groupBy(
                'atrativo.id',
                'atrativo.nome',
                'atrativo.latitude',
                'atrativo.longitude'
            )
            ->get()
            ->map(
                fn ($item) => [
                    'id' =>
                        $item->id,

                    'name' =>
                        $item->nome,

                    'latitude' =>
                        (float)
                        $item->latitude,

                    'longitude' =>
                        (float)
                        $item->longitude,

                    'intensity' =>
                        (int)
                        $item->intensity,
                ]
            )
            ->all();
    }

    private function adaptationHeatmap(
        ?Carbon $from,
        string $action
    ): array {
        return DB::table(
            'itens_adaptacao_rota as item'
        )
            ->join(
                'adaptacoes_rota as adaptacao',
                'adaptacao.id',
                '=',
                'item.adaptacao_rota_id'
            )
            ->join(
                'atrativos as atrativo',
                'atrativo.id',
                '=',
                'item.atrativo_id'
            )
            ->where(
                'item.acao',
                $action
            )
            ->whereNotNull(
                'atrativo.latitude'
            )
            ->whereNotNull(
                'atrativo.longitude'
            )
            ->when(
                $from,
                fn ($query) =>
                    $query->where(
                        'adaptacao.created_at',
                        '>=',
                        $from
                    )
            )
            ->select(
                'atrativo.id',
                'atrativo.nome',
                'atrativo.latitude',
                'atrativo.longitude',

                DB::raw(
                    'COUNT(*) AS intensity'
                )
            )
            ->groupBy(
                'atrativo.id',
                'atrativo.nome',
                'atrativo.latitude',
                'atrativo.longitude'
            )
            ->get()
            ->map(
                fn ($item) => [
                    'id' =>
                        $item->id,

                    'name' =>
                        $item->nome,

                    'latitude' =>
                        (float)
                        $item->latitude,

                    'longitude' =>
                        (float)
                        $item->longitude,

                    'intensity' =>
                        (int)
                        $item->intensity,
                ]
            )
            ->all();
    }

    private function unmetDemand(
        ?Carbon $from
    ): array {
        $events =
            JourneyEvent::query()
                ->where(
                    'event_type',
                    JourneyEventType::ROUTE_NOT_FOUND->value
                )
                ->when(
                    $from,
                    fn ($query) =>
                        $query->where(
                            'occurred_at',
                            '>=',
                            $from
                        )
                )
                ->get();

        return $events
            ->map(
                function (
                    JourneyEvent $event
                ) {
                    $interests =
                        data_get(
                            $event->payload,
                            'preferences.interests',
                            []
                        );

                    $moods =
                        data_get(
                            $event->payload,
                            'preferences.moods',
                            []
                        );

                    $intensity =
                        data_get(
                            $event->payload,
                            'preferences.intensity'
                        );

                    $primary =
                        $interests[0]
                        ?? $moods[0]
                        ?? $intensity
                        ?? 'Sem categoria';

                    return ucfirst(
                        mb_strtolower(
                            $primary
                        )
                    );
                }
            )
            ->countBy()
            ->sortDesc()
            ->take(6)
            ->map(
                fn ($count, $label) => [
                    'label' =>
                        $label,

                    'count' =>
                        $count,
                ]
            )
            ->values()
            ->all();
    }

    private function resolveStartDate(
        string $period
    ): ?Carbon {
        return match ($period) {
            '7' =>
                now()
                    ->subDays(7)
                    ->startOfDay(),

            '30' =>
                now()
                    ->subDays(30)
                    ->startOfDay(),

            '90' =>
                now()
                    ->subDays(90)
                    ->startOfDay(),

            'all' =>
                null,

            default =>
                now()
                    ->subDays(30)
                    ->startOfDay(),
        };
    }
}