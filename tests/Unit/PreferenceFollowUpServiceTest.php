<?php

namespace Tests\Unit;

use App\Services\Itinerary\PreferenceFollowUpService;
use PHPUnit\Framework\TestCase;

class PreferenceFollowUpServiceTest extends TestCase
{
    private PreferenceFollowUpService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PreferenceFollowUpService();
    }

    public function test_asks_budget_first_for_a_cost_sensitive_request(): void
    {
        $this->assertSame(
            PreferenceFollowUpService::BUDGET,
            $this->service->nextQuestion('Quero comer em um restaurante barato.')
        );
    }

    public function test_asks_time_first_when_the_request_has_no_cost_context(): void
    {
        $this->assertSame(
            PreferenceFollowUpService::TIME,
            $this->service->nextQuestion('Quero conhecer praias e natureza.')
        );
    }

    public function test_asks_budget_for_paid_context_when_value_is_missing(): void
    {
        $this->assertSame(
            PreferenceFollowUpService::BUDGET,
            $this->service->nextQuestion('Quero comer em um restaurante por duas horas.')
        );
    }

    public function test_does_not_ask_budget_for_a_context_without_cost_relevance(): void
    {
        $this->assertNull(
            $this->service->nextQuestion('Quero conhecer a natureza por duas horas.')
        );
    }

    public function test_does_not_ask_budget_when_value_was_informed(): void
    {
        $this->assertNull(
            $this->service->nextQuestion('Quero comer por duas horas e gastar até R$ 50.')
        );
    }

    public function test_budget_confirmation_prevents_repeating_the_question(): void
    {
        $this->assertNull(
            $this->service->nextQuestion(
                'Quero gastronomia por duas horas. Não tenho limite de orçamento definido.',
                budgetConfirmed: true,
            )
        );
    }
}
