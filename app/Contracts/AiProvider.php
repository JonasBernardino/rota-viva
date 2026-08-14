<?php

namespace App\Contracts;

interface AiProvider
{
    public function generateStructured(
        string $systemPrompt,
        string $userPrompt,
        array $schema
    ): array;
}