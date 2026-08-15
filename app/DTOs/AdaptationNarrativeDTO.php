<?php

namespace App\DTOs;

class AdaptationNarrativeDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $summary,
        public readonly array $changes,
    ) {}
}
