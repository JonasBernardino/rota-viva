<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Ai\AiExperienceWriter;
use App\Services\Ai\AiPreferenceInterpreter;
use App\Services\Analytics\JourneyAnalyticsService;
use App\Services\Itinerary\ItineraryPersistenceService;
use App\Services\Itinerary\ItineraryService;
use Illuminate\Http\Request;
use RuntimeException;
use App\Models\Roteiro;

class ItineraryController extends Controller
{
    public function create()
    {
        return view('pages.route-builder');
    }

    public function store(
        Request $request,
        AiPreferenceInterpreter $interpreter,
        ItineraryService $itineraryService,
        AiExperienceWriter $writer,
        ItineraryPersistenceService $persistence,
        JourneyAnalyticsService $analytics,
    ) {
        /*
        |--------------------------------------------------------------------------
        | Tempo máximo da geração
        |--------------------------------------------------------------------------
        |
        | Alguns providers de IA podem demorar mais para responder.
        |
        */
        set_time_limit(150);

        /*
        |--------------------------------------------------------------------------
        | 1. Valida entrada
        |--------------------------------------------------------------------------
        */
        $data = $request->validate([
            'description' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. IA interpreta a linguagem natural
        |--------------------------------------------------------------------------
        |
        | A IA transforma a descrição do visitante em preferências
        | estruturadas que podem ser utilizadas pelo motor de rotas.
        |
        */
        try {
            $preferences = $interpreter->interpret(
                $data['description']
            );
        } catch (RuntimeException $exception) {
            report($exception);

            /*
            |----------------------------------------------------------------------
            | Analytics: falha de interpretação
            |----------------------------------------------------------------------
            |
            | Não salvamos o texto livre digitado pelo visitante.
            |
            */
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
        |--------------------------------------------------------------------------
        | 3. Registra a demanda
        |--------------------------------------------------------------------------
        |
        | Neste ponto já conhecemos as preferências estruturadas.
        |
        | Registramos a demanda ANTES de tentar montar uma rota.
        | Isso permite saber não apenas o que foi atendido, mas também
        | aquilo que os visitantes procuraram e não encontraram.
        |
        */
        $analytics->trackRouteRequested(
            $preferences
        );

        /*
        |--------------------------------------------------------------------------
        | 4. Laravel seleciona os atrativos
        |--------------------------------------------------------------------------
        |
        | A decisão dos locais que entram na rota continua sendo
        | determinística e baseada nos dados cadastrados.
        |
        | A IA não escolhe os atrativos.
        |
        */
        try {
            $generated = $itineraryService->generate(
                $preferences
            );
        } catch (RuntimeException $exception) {
            report($exception);

            /*
            |----------------------------------------------------------------------
            | Analytics: demanda não atendida
            |----------------------------------------------------------------------
            |
            | Este é um dos dados mais importantes para o painel do gestor.
            |
            | Sabemos o que o visitante queria, mas o município não possuía
            | uma combinação de atrativos capaz de atender aquela demanda.
            |
            */
            $analytics->trackRouteNotFound(
                preferences: $preferences,
                reason: $exception->getMessage(),
            );

            return back()
                ->withInput()
                ->with(
                    'route_error',
                    'Não encontramos atrativos compatíveis com essa busca. Tente informar o tipo de experiência, tempo disponível ou interesse principal, como cultura, natureza ou gastronomia.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. IA explica o resultado
        |--------------------------------------------------------------------------
        |
        | A rota já foi escolhida pelo backend.
        |
        | Aqui a IA apenas transforma o resultado em uma apresentação
        | mais natural para o visitante.
        |
        | Se o provider de IA falhar, o AiExperienceWriter utiliza
        | o fallback local que implementamos anteriormente.
        |
        */
        $narrative = $writer->write(
            $preferences,
            $generated
        );

        /*
        |--------------------------------------------------------------------------
        | 6. Persiste o roteiro
        |--------------------------------------------------------------------------
        |
        | Salvamos:
        |
        | - solicitação original;
        | - preferências interpretadas;
        | - roteiro;
        | - itens;
        | - narrativa apresentada ao visitante.
        |
        */
        $itinerary = $persistence->store(
            description: $data['description'],
            preferences: $preferences,
            generated: $generated,
            narrative: $narrative,
        );

        /*
        |--------------------------------------------------------------------------
        | 7. Analytics: roteiro criado
        |--------------------------------------------------------------------------
        |
        | Agora sabemos que a solicitação foi efetivamente atendida.
        |
        */
        $analytics->trackRouteCreated(
            itinerary: $itinerary,
            preferences: $preferences,
        );

        /*
        |--------------------------------------------------------------------------
        | 8. Exibe o roteiro
        |--------------------------------------------------------------------------
        */
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
