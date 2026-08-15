<?php

namespace App\Services\Itinerary;

use Illuminate\Support\Str;

class PreferenceFollowUpService
{
    public const TIME = 'time';

    public const BUDGET = 'budget';

    public function nextQuestion(
        string $description,
        bool $timeConfirmed = false,
        bool $budgetConfirmed = false,
    ): ?string {
        $normalized = Str::of($description)
            ->lower()
            ->ascii()
            ->toString();

        if (
            ! $budgetConfirmed
            && $this->budgetIsRelevant($normalized)
            && ! $this->mentionsBudget($normalized)
        ) {
            return self::BUDGET;
        }

        if (! $timeConfirmed && ! $this->mentionsAvailableTime($normalized)) {
            return self::TIME;
        }

        return null;
    }

    private function mentionsAvailableTime(string $text): bool
    {
        if (preg_match('/\b\d+\s*(h|hora|horas|min|minuto|minutos)\b/', $text) === 1) {
            return true;
        }

        if (preg_match('/\b\d+\s*(a|ate|-)\s*\d+\s*(h|hora|horas)\b/', $text) === 1) {
            return true;
        }

        if (preg_match('/\b(?:uma|duas|tres|quatro|cinco|seis|sete|oito)\s+horas?\b/', $text) === 1) {
            return true;
        }

        return $this->containsAny($text, [
            'manha toda',
            'tarde toda',
            'noite toda',
            'dia inteiro',
            'meio periodo',
            'periodo inteiro',
            'pouco tempo',
            'tempo livre',
        ]);
    }

    private function budgetIsRelevant(string $text): bool
    {
        return $this->containsAny($text, [
            'barato',
            'barata',
            'economico',
            'economica',
            'baixo custo',
            'em conta',
            'gastar',
            'gasto',
            'orcamento',
            'preco',
            'custo',
            'valor',
            'gratuito',
            'gratuita',
            'gratuitos',
            'gratuitas',
            'gratis',
            'sem pagar',
            'comida',
            'comer',
            'restaurante',
            'gastronomia',
            'pousada',
            'hospedagem',
            'hotel',
            'onde ficar',
            'compras',
            'passeio de barco',
        ]);
    }

    private function mentionsBudget(string $text): bool
    {
        if (preg_match('/\b(?:somente|apenas)\b.{0,30}\b(?:gratuit\w*|gratis)\b/', $text) === 1) {
            return true;
        }

        if ($this->containsAny($text, [
            'somente gratuito',
            'somente gratuita',
            'somente gratuitos',
            'somente gratuitas',
            'somente gratis',
            'apenas gratuito',
            'apenas gratuita',
            'apenas gratuitos',
            'apenas gratuitas',
            'apenas gratis',
            'sem gastar',
            'sem custo',
            'sem limite de orcamento',
            'sem limite definido',
        ])) {
            return true;
        }

        foreach ([
            '/(?:r\$|rs)\s*\d{1,5}(?:[,.]\d{1,2})?/',
            '/\d{1,5}(?:[,.]\d{1,2})?\s*(?:reais|real)\b/',
            '/(?:orcamento|gastar|gasto|limite|custo|valor)\D{0,24}\d{1,5}(?:[,.]\d{1,2})?/',
        ] as $pattern) {
            if (preg_match($pattern, $text) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, string>  $terms
     */
    private function containsAny(string $text, array $terms): bool
    {
        foreach ($terms as $term) {
            if (str_contains($text, $term)) {
                return true;
            }
        }

        return false;
    }
}
