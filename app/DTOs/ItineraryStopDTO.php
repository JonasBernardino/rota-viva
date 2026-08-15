<?php

namespace App\DTOs;

class ItineraryStopDTO
{
    public function __construct(
        public readonly int $placeId,
        public readonly string $name,
        public readonly string $category,
        public readonly int $durationMinutes,
        public readonly float $estimatedCost,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly bool $isOutdoor,
        public readonly int $score,
    ) {}
}
