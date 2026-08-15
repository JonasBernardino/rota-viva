<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Atrativo;
use App\Models\Roteiro;
use App\Services\Ai\AiExperienceWriter;
use App\Services\Ai\AiPreferenceInterpreter;
use App\Services\Analytics\JourneyAnalyticsService;
use App\Services\Itinerary\ItineraryPersistenceService;
use App\Services\Itinerary\ItineraryService;
use App\DTOs\VisitorPreferencesDTO;
use Illuminate\Http\Request;
use RuntimeException;

class ItineraryController extends Controller
{
    public function create(Request $request)
    {
        $initialQuery = trim((string) $request->query(
            'q',
            $request->query('description', '')
        ));

        return view('pages.route-builder', [
            'initialQuery' => mb_substr($initialQuery, 0, 2000),
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
                'min:3',
                'max:2000',
            ],
        ], [
            'description.required' => 'Conte em poucas palavras o que você procura para criarmos sua rota.',
            'description.min' => 'Digite pelo menos 3 caracteres, por exemplo: comida, praia ou cultura.',
            'description.max' => 'Sua descrição ficou muito longa. Tente resumir em até 2000 caracteres.',
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

        if (! $request->boolean('time_confirmed') && ! $this->descriptionMentionsAvailableTime($data['description'])) {
            return back()
                ->withInput()
                ->with([
                    'needs_time_question' => true,
                ]);
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
                    [
                        'route_error' => 'Não conseguimos montar uma rota completa com esses critérios, mas encontramos caminhos para você continuar planejando.',
                        'route_suggestions' => $this->suggestPreferenceAdjustments($preferences),
                        'route_alternatives' => $this->alternativePlaces($preferences),
                    ]
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

    /**
     * @return array<int, string>
     */
    private function suggestPreferenceAdjustments(VisitorPreferencesDTO $preferences): array
    {
        $suggestions = [
            'Tente buscar por interesses mais amplos, como cultura, natureza, gastronomia ou lazer.',
        ];

        if ($preferences->availableMinutes !== null && $preferences->availableMinutes < 120) {
            $suggestions[] = 'Aumente um pouco o tempo disponível para permitir pelo menos uma parada completa.';
        } else {
            $suggestions[] = 'Informe quanto tempo você tem, por exemplo: tenho 2 horas ou tenho uma tarde livre.';
        }

        if ($preferences->budget !== null && $preferences->budget <= 50) {
            $suggestions[] = 'Se possível, aumente o orçamento ou peça explicitamente por opções gratuitas.';
        } else {
            $suggestions[] = 'Você também pode informar se prefere opções gratuitas ou de baixo custo.';
        }

        if ($preferences->accessibilityRequirements !== []) {
            $suggestions[] = 'Caso alguma necessidade de acessibilidade seja flexível, descreva melhor o tipo de apoio necessário.';
        }

        if ($preferences->hasChildren === true) {
            $suggestions[] = 'Para passeios com crianças, tente combinar interesses como família, cultura leve, praça, praia calma ou gastronomia.';
        }

        return array_values(array_unique(array_slice($suggestions, 0, 5)));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function alternativePlaces(VisitorPreferencesDTO $preferences): array
    {
        return Atrativo::query()
            ->with(['categoria', 'midias'])
            ->where('is_disponivel', true)
            ->get()
            ->map(fn (Atrativo $place): array => [
                'place' => $place,
                'score' => $this->alternativeScore($place, $preferences),
            ])
            ->sortByDesc('score')
            ->take(3)
            ->map(function (array $candidate): array {
                /** @var Atrativo $place */
                $place = $candidate['place'];
                $media = $place->midias->firstWhere('is_destaque', true) ?? $place->midias->first();
                $mediaUrl = $media?->url;

                return [
                    'name' => $place->nome,
                    'slug' => $place->slug,
                    'category' => $place->categoria?->nome ?? 'Atrativo oficial',
                    'duration' => $place->duracao_minutos,
                    'cost' => $place->custo_medio,
                    'description' => str($place->descricao ?? 'Atrativo oficial disponível no município.')->limit(120)->toString(),
                    'image_url' => $mediaUrl
                        ? (str_starts_with($mediaUrl, 'http://') || str_starts_with($mediaUrl, 'https://') || str_starts_with($mediaUrl, '/')
                            ? $mediaUrl
                            : asset('storage/'.$mediaUrl))
                        : null,
                ];
            })
            ->values()
            ->all();
    }

    private function alternativeScore(Atrativo $place, VisitorPreferencesDTO $preferences): float
    {
        $score = 0.0;
        $categorySlug = $place->categoria?->slug ?? '';
        $tags = $place->tags ?? [];

        foreach ($preferences->interests as $interest) {
            if ($categorySlug === $interest || in_array($interest, $tags, true)) {
                $score += 30;
            }
        }

        foreach ($preferences->moods as $mood) {
            if (in_array($mood, $tags, true)) {
                $score += 10;
            }
        }

        if ($preferences->hasChildren === true && $place->adequado_criancas) {
            $score += 12;
        }

        if ($preferences->budget !== null && $place->custo_medio <= $preferences->budget) {
            $score += 8;
        }

        if ($preferences->availableMinutes !== null && $place->duracao_minutos <= $preferences->availableMinutes) {
            $score += 8;
        }

        return $score;
    }

    private function descriptionMentionsAvailableTime(string $description): bool
    {
        $normalized = str($description)
            ->lower()
            ->ascii()
            ->toString();

        if (preg_match('/\b\d+\s*(h|hora|horas|min|minuto|minutos)\b/', $normalized) === 1) {
            return true;
        }

        if (preg_match('/\b\d+\s*(a|ate|-)\s*\d+\s*(h|hora|horas)\b/', $normalized) === 1) {
            return true;
        }

        foreach ([
            'manha toda',
            'tarde toda',
            'noite toda',
            'dia inteiro',
            'meio periodo',
            'periodo inteiro',
            'pouco tempo',
            'tempo livre',
        ] as $term) {
            if (str_contains($normalized, $term)) {
                return true;
            }
        }

        return false;
    }
}
