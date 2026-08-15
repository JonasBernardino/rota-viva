<?php

namespace App\Services\Itinerary;

use App\DTOs\ExperienceNarrativeDTO;
use App\DTOs\GeneratedItineraryDTO;
use App\DTOs\VisitorPreferencesDTO;
use App\Models\PreferenciaVisitante;
use App\Models\Roteiro;
use Illuminate\Support\Facades\DB;

class ItineraryPersistenceService
{
    public function store(
        string $description,
        VisitorPreferencesDTO $preferences,
        GeneratedItineraryDTO $generated,
        ExperienceNarrativeDTO $narrative
    ): Roteiro {
        return DB::transaction(function () use (
            $description,
            $preferences,
            $generated,
            $narrative
        ): Roteiro {
            $preference = PreferenciaVisitante::create([
                'descricao_original' => $description,
                'humores' => $preferences->moods,
                'interesses' => $preferences->interests,
                'minutos_disponiveis' => $preferences->availableMinutes,
                'orcamento' => $preferences->budget,
                'tem_criancas' => $preferences->hasChildren,
                'transporte' => $preferences->transport,
                'requisitos_acessibilidade' => $preferences->accessibilityRequirements,
                'intensidade' => $preferences->intensity,
            ]);

            /** @var Roteiro $roteiro */
            $roteiro = Roteiro::create([
                'preferencia_visitante_id' => $preference->id,
                'titulo' => $narrative->title,
                'resumo' => $narrative->summary,
                'duracao_total_minutos' => $generated->totalDurationMinutes,
                'custo_total_estimado' => $generated->totalEstimatedCost,
                'status' => 'ACTIVE',
            ]);

            foreach ($generated->stops as $position => $stop) {
                $roteiro->itens()->create([
                    'atrativo_id' => $stop->placeId,
                    'posicao' => $position + 1,
                    'duracao_minutos' => $stop->durationMinutes,
                    'custo_estimado' => $stop->estimatedCost,
                    'motivo' => $narrative->reasonFor($stop->placeId),
                ]);
            }

            return $roteiro;
        });
    }
}
