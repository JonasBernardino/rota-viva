<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Roteiro;
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
            'initialQuery' => $request->query('q'),
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

        // 1. IA interpreta linguagem natural. Se ela falhar, o interpretador usa fallback local.
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
        try {
            $generated = $itineraryService->generate(
                $preferences
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with(
                    'ai_error',
                    'Não encontramos atrativos compatíveis com essa busca. Tente informar o tipo de experiência, tempo disponível ou interesse principal, como cultura, natureza ou gastronomia.'
                );
        }

        // 3. IA explica o resultado. Se ela falhar, o escritor usa fallback local.
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
        Roteiro $itinerary
    ) {
        $itinerary->load([
            'preferencia',
            'itens.atrativo.categoria',
        ]);

        return view(
            'pages.route-result',
            compact('itinerary')
        );
    }
}
