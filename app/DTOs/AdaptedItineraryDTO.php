<?php

namespace App\DTOs;

class AdaptedItineraryDTO
{
    /**
     * @param  ItineraryStopDTO[]  $stops
     * @param  int[]  $removedPlaceIds
     * @param  int[]  $addedPlaceIds
     */
    public function __construct(
        public readonly array $stops,
        public readonly array $removedPlaceIds,
        public readonly array $addedPlaceIds,
        public readonly int $totalDurationMinutes,
        public readonly float $totalEstimatedCost,
    ) {}
}
