<?php

namespace App\DTOs;

class GeneratedItineraryDTO
{
    /**
     * @param ItineraryStopDTO[] $stops
     */
    public function __construct(
        public readonly array $stops,
        public readonly int $totalDurationMinutes,
        public readonly float $totalEstimatedCost,
    ) {
    }
}