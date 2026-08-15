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

class ItineraryController extends Controller
{
    public function create(Request $request)
    {
        return view('pages.route-builder', [
            'initialQuery' =>
            $request->query('q'),
        ]);
    }

    public function store(
        Request $request,
        AiPreferenceInterpreter $interpreter,
        ItineraryService $itineraryService,
        AiExperienceWriter $writer,
        ItineraryPersistenceService $persistence
    ) {
        $data = $request->validate([
            'description' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
        ]);

        // 1. Gemini interpreta linguagem natural.
        try {
            $preferences = $interpreter->interpret(
                $data['description']
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with(
                    'ai_error',
                    'Não conseguimos interpretar sua solicitação agora. Tente novamente em alguns instantes.'
                );
        }

        // 2. Laravel seleciona os locais.
        $generated = $itineraryService->generate(
            $preferences
        );

        // 3. Gemini explica o resultado.
        $narrative = $writer->write(
            $preferences,
            $generated
        );

        // 4. Salva rota e preferências.
        $itinerary = $persistence->store(
            description: $data['description'],
            preferences: $preferences,
            generated: $generated,
            narrative: $narrative,
        );

        // 5. Exibe rota.
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
