<?php

namespace App\Services\Ai;

use App\DTOs\VisitorPreferencesDTO;
use Illuminate\Support\Str;

class LocalPreferenceInterpreter
{
    public function interpret(string $description): VisitorPreferencesDTO
    {
        $normalized = Str::of($description)
            ->lower()
            ->ascii()
            ->toString();

        return new VisitorPreferencesDTO(
            moods: $this->detectMoods($normalized),
            interests: $this->detectInterests($normalized),
            availableMinutes: $this->detectAvailableMinutes($normalized),
            budget: $this->detectBudget($normalized),
            hasChildren: $this->detectChildren($normalized),
            transport: $this->detectTransport($normalized),
            accessibilityRequirements: $this->detectAccessibility($normalized),
            intensity: $this->detectIntensity($normalized),
            missingInformation: [],
        );
    }

    /**
     * @return array<int, string>
     */
    private function detectMoods(string $text): array
    {
        $moods = [];

        foreach ([
            'tranquilo' => ['tranquilo', 'calmo', 'leve', 'relax'],
            'cultural' => ['cultura', 'histor', 'memoria', 'patrimonio'],
            'familia' => ['familia', 'crianca', 'filho'],
            'aventura' => ['aventura', 'trilha', 'radical'],
        ] as $mood => $keywords) {
            if ($this->containsAny($text, $keywords)) {
                $moods[] = $mood;
            }
        }

        return array_values(array_unique($moods));
    }

    /**
     * @return array<int, string>
     */
    private function detectInterests(string $text): array
    {
        $interests = [];

        foreach ([
            'patrimonio-historico' => ['historia', 'historico', 'igreja', 'patrimonio', 'memoria'],
            'cultura-e-tradicao' => ['cultura', 'artesanato', 'rendeira', 'tradicao', 'centro cultural'],
            'gastronomia-litoranea' => ['gastronomia', 'comer', 'restaurante', 'mercado', 'peixe', 'sabores'],
            'sol-e-natureza' => ['natureza', 'praia', 'mirante', 'mar', 'coqueiro', 'paisagem'],
            'ecoturismo-e-trilhas' => ['ecoturismo', 'trilha', 'manguezal', 'rio', 'barco'],
            'praias-e-lazer' => ['lazer', 'banho', 'familia', 'crianca', 'pousada', 'hospedagem'],
        ] as $interest => $keywords) {
            if ($this->containsAny($text, $keywords)) {
                $interests[] = $interest;
            }
        }

        return array_values(array_unique($interests ?: ['cultura-e-tradicao', 'sol-e-natureza']));
    }

    private function detectAvailableMinutes(string $text): ?int
    {
        if (preg_match('/(\d+)\s*(?:a|ate|-)\s*(\d+)\s*(h|hora|horas)/', $text, $matches) === 1) {
            return ((int) $matches[2]) * 60;
        }

        if (preg_match('/mais de\s*(\d+)\s*(h|hora|horas)/', $text, $matches) === 1) {
            return (((int) $matches[1]) + 2) * 60;
        }

        if (preg_match('/(\d+)\s*(h|hora|horas)/', $text, $matches) === 1) {
            return ((int) $matches[1]) * 60;
        }

        if (preg_match('/(\d+)\s*(min|minuto|minutos)/', $text, $matches) === 1) {
            return (int) $matches[1];
        }

        if ($this->containsAny($text, ['manha toda', 'tarde toda', 'noite toda', 'meio periodo'])) {
            return 240;
        }

        if ($this->containsAny($text, ['dia inteiro', 'periodo inteiro'])) {
            return 480;
        }

        return null;
    }

    private function detectBudget(string $text): ?float
    {
        if (preg_match('/(?:r\$|rs|\$)?\s*(\d{2,5})(?:[,.](\d{2}))?/', $text, $matches) !== 1) {
            return null;
        }

        $value = (float) $matches[1];

        if (isset($matches[2]) && $matches[2] !== '') {
            $value += ((float) $matches[2]) / 100;
        }

        return $value;
    }

    private function detectChildren(string $text): ?bool
    {
        if ($this->containsAny($text, ['crianca', 'filho', 'familia', 'familias'])) {
            return true;
        }

        return null;
    }

    private function detectTransport(string $text): ?string
    {
        return match (true) {
            $this->containsAny($text, ['a pe', 'caminhando', 'caminhada']) => 'walking',
            $this->containsAny($text, ['bicicleta', 'bike']) => 'bicycle',
            $this->containsAny($text, ['onibus', 'transporte publico']) => 'public_transport',
            $this->containsAny($text, ['carro', 'uber', 'taxi']) => 'car',
            default => null,
        };
    }

    /**
     * @return array<int, string>
     */
    private function detectAccessibility(string $text): array
    {
        $requirements = [];

        foreach ([
            'rampa-de-acesso' => ['rampa', 'mobilidade', 'cadeirante', 'cadeira de rodas'],
            'banheiro-adaptado' => ['banheiro adaptado'],
            'atendimento-em-libras' => ['libras'],
            'piso-tatil' => ['piso tatil'],
            'audiodescricao' => ['audiodescricao', 'audio descricao'],
            'sinalizacao-braille' => ['braille'],
        ] as $requirement => $keywords) {
            if ($this->containsAny($text, $keywords)) {
                $requirements[] = $requirement;
            }
        }

        return $requirements;
    }

    private function detectIntensity(string $text): ?string
    {
        return match (true) {
            $this->containsAny($text, ['leve', 'tranquilo', 'calmo', 'pouca caminhada']) => 'low',
            $this->containsAny($text, ['intenso', 'radical', 'aventura']) => 'high',
            $this->containsAny($text, ['moderado', 'medio']) => 'medium',
            default => 'low',
        };
    }

    /**
     * @param  array<int, string>  $keywords
     */
    private function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
