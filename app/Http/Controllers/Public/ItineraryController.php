<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Itinerary;
use App\Services\Ai\AiExperienceWriter;
use App\Services\Ai\AiPreferenceInterpreter;
use App\Services\Itinerary\ItineraryPersistenceService;
use App\Services\Itinerary\ItineraryService;
use Illuminate\Http\Request;
use RuntimeException;
use App\Services\Analytics\JourneyAnalyticsService;

class ItineraryController extends Controller
{
    public function create(Request $request)
    {
        return view('pages.route-builder', [
            'initialQuery' => $request->query('q'),
        ]);
    }

    public function store(
        Request $request,
        AiPreferenceInterpreter $interpreter,
        ItineraryService $itineraryService,
        AiExperienceWriter $writer,
        ItineraryPersistenceService $persistence,
        JourneyAnalyticsService $analytics,
    ) {
        set_time_limit(150);

        $data = $request->validate([
            'description' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
        ]);

        /*
     * 1. Interpretar linguagem natural.
     */
        try {
            $preferences = $interpreter->interpret(
                $data['description']
            );
        } catch (RuntimeException $exception) {
            report($exception);

            $analytics->trackAiInterpretationFailed(
                $exception->getMessage()
            );

            return back()
                ->withInput()
                ->with(
                    'ai_error',
                    'Não conseguimos interpretar sua solicitação agora. Tente novamente em alguns instantes.'
                );
        }

        /*
     * 2. Agora conhecemos a demanda.
     *
     * Registramos ANTES de tentar gerar uma rota.
     */
        $analytics->trackRouteRequested(
            $preferences
        );

        /*
     * 3. Motor determinístico.
     */
        try {
            $generated =
                $itineraryService->generate(
                    $preferences
                );
        } catch (RuntimeException $exception) {
            report($exception);

            /*
         * ESTE É O DADO MUITO IMPORTANTE:
         *
         * sabemos o que o visitante queria,
         * mas não conseguimos atendê-lo.
         */
            $analytics->trackRouteNotFound(
                preferences: $preferences,
                reason: $exception->getMessage(),
            );

            return back()
                ->withInput()
                ->with(
                    'route_error',
                    'Não encontramos experiências compatíveis com suas preferências neste momento. Tente ajustar tempo, orçamento ou tipo de experiência.'
                );
        }

        /*
     * 4. IA escreve a apresentação.
     *
     * O AiExperienceWriter já possui fallback.
     */
        $narrative = $writer->write(
            $preferences,
            $generated
        );

        /*
     * 5. Persistência da rota.
     */
        $itinerary = $persistence->store(
            description: $data['description'],
            preferences: $preferences,
            generated: $generated,
            narrative: $narrative,
        );

        /*
     * 6. Analytics de sucesso.
     */
        $analytics->trackRouteCreated(
            itinerary: $itinerary,
            preferences: $preferences,
        );

        return redirect()->route(
            'routes.show',
            $itinerary
        );
    }

    public function show(
        Itinerary $itinerary
    ) {
        $itinerary->load([
            'preference',
            'items.place.category',
        ]);

        return view(
            'pages.route-result',
            compact('itinerary')
        );
    }
}
