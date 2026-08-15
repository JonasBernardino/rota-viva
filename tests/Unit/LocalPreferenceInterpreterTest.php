<?php

namespace Tests\Unit;

use App\Services\Ai\LocalPreferenceInterpreter;
use PHPUnit\Framework\TestCase;

class LocalPreferenceInterpreterTest extends TestCase
{
    public function test_extracts_explicit_budget_without_confusing_time_with_money(): void
    {
        $interpreter = new LocalPreferenceInterpreter();

        $preferences = $interpreter->interpret(
            'Tenho 45 minutos e meu orçamento máximo é de R$ 50.'
        );

        $this->assertSame(45, $preferences->availableMinutes);
        $this->assertSame(50.0, $preferences->budget);
    }

    public function test_does_not_treat_duration_as_budget(): void
    {
        $preferences = (new LocalPreferenceInterpreter())->interpret(
            'Quero gastronomia e tenho 90 minutos.'
        );

        $this->assertSame(90, $preferences->availableMinutes);
        $this->assertNull($preferences->budget);
    }

    public function test_understands_free_options_as_zero_budget(): void
    {
        $preferences = (new LocalPreferenceInterpreter())->interpret(
            'Quero somente opções gratuitas por duas horas.'
        );

        $this->assertSame(0.0, $preferences->budget);
    }
}
