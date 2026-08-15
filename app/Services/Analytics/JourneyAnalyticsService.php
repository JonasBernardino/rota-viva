<?php

namespace App\Services\Analytics;

use App\DTOs\AdaptedItineraryDTO;
use App\DTOs\VisitorPreferencesDTO;
use App\Enums\JourneyEventType;
use App\Models\AdaptacaoRota;
use App\Models\JourneyEvent;
use App\Models\Roteiro;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Throwable;

class JourneyAnalyticsService
{
    public function trackRouteRequested(
        VisitorPreferencesDTO $preferences
    ): void {
        $this->record(
            JourneyEventType::ROUTE_REQUESTED,
            [
                'preferences' => $this->preferencesPayload(
                    $preferences
                ),
            ]
        );
    }

    public function trackRouteCreated(
        Roteiro $itinerary,
        VisitorPreferencesDTO $preferences
    ): void {
        $this->record(
            JourneyEventType::ROUTE_CREATED,
            [
                /*
                 * Mantemos o payload analítico em inglês,
                 * mas lemos os campos novos do Model Roteiro.
                 */
                'itinerary_id' => $itinerary->id,

                'total_duration_minutes' => $itinerary->duracao_total_minutos,

                'total_estimated_cost' => $itinerary->custo_total_estimado,

                'preferences' => $this->preferencesPayload(
                    $preferences
                ),
            ]
        );
    }

    public function trackRouteNotFound(
        VisitorPreferencesDTO $preferences,
        string $reason
    ): void {
        $this->record(
            JourneyEventType::ROUTE_NOT_FOUND,
            [
                'reason' => $reason,

                'preferences' => $this->preferencesPayload(
                    $preferences
                ),
            ]
        );
    }

    public function trackRouteAdapted(
        AdaptacaoRota $adaptation,
        AdaptedItineraryDTO $adapted
    ): void {
        $this->record(
            JourneyEventType::ROUTE_ADAPTED,
            [
                /*
                 * Payload analítico continua em inglês.
                 *
                 * O Model AdaptacaoRota, porém,
                 * usa os campos em português.
                 */
                'itinerary_id' => $adaptation->roteiro_id,

                'adaptation_id' => $adaptation->id,

                'event' => $adaptation->evento,

                'removed_place_ids' => $adapted->removedPlaceIds,

                'added_place_ids' => $adapted->addedPlaceIds,

                'total_duration_minutes' => $adapted->totalDurationMinutes,

                'total_estimated_cost' => $adapted->totalEstimatedCost,
            ]
        );
    }

    public function trackAiInterpretationFailed(
        string $reason
    ): void {
        /*
         * Não persistimos o texto livre digitado
         * pelo visitante.
         */
        $this->record(
            JourneyEventType::AI_INTERPRETATION_FAILED,
            [
                'reason' => $reason,
            ]
        );
    }

    private function preferencesPayload(
        VisitorPreferencesDTO $preferences
    ): array {
        return [
            'moods' => $preferences->moods,

            'interests' => $preferences->interests,

            'available_minutes' => null,

            'budget' => null,

            'duration_range' => $this->durationRange(
                $preferences->availableMinutes
            ),

            'budget_range' => $this->budgetRange(
                $preferences->budget
            ),

            'has_children' => $preferences->hasChildren,

            'transport' => $preferences->transport,

            'accessibility_requirements' => $preferences->accessibilityRequirements,

            'intensity' => $preferences->intensity,

            'missing_information_count' => count(
                $preferences->missingInformation
            ),
        ];
    }

    private function budgetRange(?float $budget): string
    {
        if ($budget === null) {
            return 'Não informado';
        }

        if ($budget <= 50) {
            return 'Até R$ 50';
        }

        if ($budget <= 150) {
            return 'R$ 51–150';
        }

        if ($budget <= 300) {
            return 'R$ 151–300';
        }

        return 'Acima de R$ 300';
    }

    private function durationRange(?int $minutes): string
    {
        if ($minutes === null) {
            return 'Não informado';
        }

        if ($minutes <= 120) {
            return 'Até 2h';
        }

        if ($minutes <= 240) {
            return '2h–4h';
        }

        if ($minutes <= 360) {
            return '4h–6h';
        }

        return 'Mais de 6h';
    }

    private function record(
        JourneyEventType $event,
        array $payload
    ): void {
        /*
         * Analytics nunca deve derrubar a jornada.
         */
        try {
            JourneyEvent::create([
                'session_uuid' => $this->sessionUuid(),

                'event_type' => $event->value,

                'payload' => $payload,

                'occurred_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Log::warning(
                'Falha ao registrar evento analítico.',
                [
                    'event' => $event->value,

                    'error' => $exception->getMessage(),
                ]
            );
        }
    }

    private function sessionUuid(): string
    {
        $uuid = Session::get(
            'rota_viva_journey_uuid'
        );

        if ($uuid) {
            return $uuid;
        }

        $uuid =
            (string) Str::uuid();

        Session::put(
            'rota_viva_journey_uuid',
            $uuid
        );

        return $uuid;
    }
}
