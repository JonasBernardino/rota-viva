<?php

namespace App\DTOs;

class VisitorPreferencesDTO
{
    public function __construct(
        public readonly array $moods,
        public readonly array $interests,
        public readonly ?int $availableMinutes,
        public readonly ?float $budget,
        public readonly ?bool $hasChildren,
        public readonly ?string $transport,
        public readonly array $accessibilityRequirements,
        public readonly ?string $intensity,
        public readonly array $missingInformation,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            moods: $data['moods'] ?? [],
            interests: $data['interests'] ?? [],
            availableMinutes: $data['available_minutes'] ?? null,
            budget: $data['budget'] ?? null,
            hasChildren: $data['has_children'] ?? null,
            transport: $data['transport'] ?? null,
            accessibilityRequirements: $data['accessibility_requirements'] ?? [],
            intensity: $data['intensity'] ?? null,
            missingInformation: $data['missing_information'] ?? [],
        );
    }
}