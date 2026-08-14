<?php

namespace App\DTOs;

class ExperienceNarrativeDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $summary,
        public readonly array $reasons,
    ) {
    }

    public function reasonFor(int $placeId): string
    {
        return $this->reasons[$placeId]
            ?? 'Selecionado por ser compatível com suas preferências.';
    }
}