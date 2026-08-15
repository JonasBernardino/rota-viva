<?php

namespace App\Services\Analytics;

use App\DTOs\AdaptedItineraryDTO;
use App\DTOs\VisitorPreferencesDTO;
use App\Enums\JourneyEventType;
use App\Models\Itinerary;
use App\Models\JourneyEvent;
use App\Models\RouteAdaptation;
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
        Itinerary $itinerary,
        VisitorPreferencesDTO $preferences
    ): void {
        $this->record(
            JourneyEventType::ROUTE_CREATED,
            [
                'itinerary_id' => $itinerary->id,

                'total_duration_minutes' =>
                    $itinerary->total_duration_minutes,

                'total_estimated_cost' =>
                    $itinerary->total_estimated_cost,

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
        RouteAdaptation $adaptation,
        AdaptedItineraryDTO $adapted
    ): void {
        $this->record(
            JourneyEventType::ROUTE_ADAPTED,
            [
                'itinerary_id' =>
                    $adaptation->itinerary_id,

                'adaptation_id' =>
                    $adaptation->id,

                'event' =>
                    $adaptation->event,

                'removed_place_ids' =>
                    $adapted->removedPlaceIds,

                'added_place_ids' =>
                    $adapted->addedPlaceIds,

                'total_duration_minutes' =>
                    $adapted->totalDurationMinutes,

                'total_estimated_cost' =>
                    $adapted->totalEstimatedCost,
            ]
        );
    }

    public function trackAiInterpretationFailed(
        string $reason
    ): void {
        /*
         * Não persistimos o texto livre digitado pelo
         * visitante neste evento.
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
            'moods' =>
                $preferences->moods,

            'interests' =>
                $preferences->interests,

            'available_minutes' =>
                $preferences->availableMinutes,

            'budget' =>
                $preferences->budget,

            'has_children' =>
                $preferences->hasChildren,

            'transport' =>
                $preferences->transport,

            'accessibility_requirements' =>
                $preferences->accessibilityRequirements,

            'intensity' =>
                $preferences->intensity,

            'missing_information' =>
                $preferences->missingInformation,
        ];
    }

    private function record(
        JourneyEventType $event,
        array $payload
    ): void {
        /*
         * Analytics nunca deve derrubar a jornada.
         *
         * Se houver erro nessa gravação, registramos
         * log e deixamos o fluxo principal continuar.
         */
        try {
            JourneyEvent::create([
                'session_uuid' =>
                    $this->sessionUuid(),

                'event_type' =>
                    $event->value,

                'payload' =>
                    $payload,

                'occurred_at' =>
                    now(),
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

        $uuid = (string) Str::uuid();

        Session::put(
            'rota_viva_journey_uuid',
            $uuid
        );

        return $uuid;
    }
}