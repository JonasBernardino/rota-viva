@extends('layouts.public')

@php
    $mapStops = $itinerary->itens
        ->map(function ($item) {
            return [
                'id' => $item->atrativo->id,

                'name' => $item->atrativo->nome,

                'category' => $item->atrativo->categoria?->nome,

                'latitude' => $item->atrativo->latitude,

                'longitude' => $item->atrativo->longitude,

                'duration' => $item->duracao_minutos,

                'cost' => $item->custo_estimado,
            ];
        })
        ->values();
@endphp


@section('title', $itinerary->titulo)

@section('description', $itinerary->resumo)


@section('content')

    {{-- =========================================================
         CABEÇALHO DA ROTA
    ========================================================== --}}

    <section class="page-hero page-hero--compact">

        <div class="page-container page-hero__content">

            <p class="eyebrow">
                Sua rota
            </p>

            <h1>
                {{ $itinerary->titulo }}
            </h1>

            <p>
                {{ $itinerary->resumo }}
            </p>


            <div class="route-result-summary">

                <span>
                    ◷
                    {{ $itinerary->duracao_total_minutos }}
                    minutos
                </span>

                <span>
                    R$
                    {{ number_format($itinerary->custo_total_estimado, 2, ',', '.') }}
                </span>

                <span>
                    {{ $itinerary->itens->count() }}

                    {{ $itinerary->itens->count() === 1 ? 'parada' : 'paradas' }}
                </span>

            </div>

        </div>

    </section>


    {{-- =========================================================
         CONTEÚDO DA ROTA
    ========================================================== --}}

    <section class="page-section">

        <div class="page-container">

            @include('partials.ai-disclaimer')

            <div class="route-result-layout">


                {{-- =====================================================
                     TIMELINE DAS PARADAS
                ====================================================== --}}

                <div class="route-timeline">

                    @foreach ($itinerary->itens as $item)
                        <article class="route-stop">

                            <div class="route-stop__number">

                                {{ str_pad((string) $item->posicao, 2, '0', STR_PAD_LEFT) }}

                            </div>


                            <div class="route-stop__content">

                                <p class="eyebrow">

                                    {{ $item->atrativo->categoria?->nome ?? 'Experiência' }}

                                </p>


                                <h2>
                                    {{ $item->atrativo->nome }}
                                </h2>


                                <div class="route-stop__meta">

                                    <span>

                                        ◷
                                        {{ $item->duracao_minutos }}
                                        min

                                    </span>


                                    <span>

                                        R$
                                        {{ number_format($item->custo_estimado, 2, ',', '.') }}

                                    </span>

                                </div>


                                @if ($item->motivo)
                                    <p>
                                        {{ $item->motivo }}
                                    </p>
                                @endif


                                {{-- =================================================
                                     NAVEGAÇÃO EXTERNA
                                ================================================== --}}

                                @if ($item->atrativo->latitude && $item->atrativo->longitude)
                                    <div class="route-stop__navigation">

                                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $item->atrativo->latitude }},{{ $item->atrativo->longitude }}"
                                            target="_blank" rel="noopener noreferrer" class="text-link">
                                            Abrir no Google Maps
                                            <span aria-hidden="true">
                                                →
                                            </span>
                                        </a>

                                    </div>
                                @endif

                            </div>

                        </article>
                    @endforeach

                </div>


                {{-- =====================================================
                     MAPA
                ====================================================== --}}

                <aside class="route-result-map">

                    <div class="route-map-wrapper">

                        <div id="route-map" class="route-map-container" data-route-map data-map-type="route"
                            data-stops='@json($mapStops)' aria-label="Mapa das paradas da sua rota"></div>


                        {{-- =================================================
                             ERRO DE GEOLOCALIZAÇÃO
                        ================================================== --}}

                        <div class="route-map-location-warning" data-location-warning hidden role="status">

                            <span aria-hidden="true">
                                ⌖
                            </span>

                            <div>

                                <strong>
                                    Não conseguimos acessar sua localização.
                                </strong>

                                <p>
                                    Você ainda pode visualizar normalmente
                                    as paradas da sua rota.
                                </p>

                            </div>

                        </div>


                        {{-- =================================================
                             FALLBACK DO MAPA
                        ================================================== --}}

                        <div class="route-map-fallback" data-map-fallback hidden role="status">

                            <span class="route-map-fallback__icon" aria-hidden="true">
                                ⌖
                            </span>

                            <div>

                                <p class="eyebrow">
                                    Mapa indisponível
                                </p>

                                <h3>
                                    Não foi possível carregar o mapa.
                                </h3>

                                <p>
                                    Sua rota continua disponível.
                                    Use os botões de navegação das paradas
                                    para abrir o Google Maps.
                                </p>

                            </div>

                        </div>

                    </div>

                </aside>

            </div>


            {{-- =========================================================
                 ROTA ADAPTATIVA
            ========================================================== --}}

            <div class="route-adaptation-callout">

                <div>

                    <p class="eyebrow">
                        Rota adaptativa
                    </p>

                    <h2>
                        Sua realidade mudou?
                    </h2>

                    <p>
                        Se começou a chover ou algo mudou
                        durante o percurso, podemos adaptar
                        somente o necessário.
                    </p>

                </div>


                <form
                    action="{{ route('routes.adapt.rain', $itinerary) }}"
                    method="post"
                    data-route-builder-form
                    data-loading-label="Adaptando sua rota..."
                    data-loading-title="Adaptando sua rota por causa da chuva"
                    data-loading-description="Estamos preservando o que ainda funciona e buscando alternativas cobertas compatíveis com seu tempo, orçamento e perfil.">

                    @csrf

                    <p class="route-builder-status" data-route-builder-status hidden role="status">
                        Estamos analisando quais paradas continuam seguras e quais precisam ser substituídas por opções cobertas.
                    </p>

                    <button class="route-cta" type="submit" data-route-builder-submit>
                        <span data-route-builder-submit-label>☂ Começou a chover</span>
                    </button>

                </form>

            </div>

        </div>

    </section>

    <div class="route-builder-loading" data-route-builder-loading hidden role="status" aria-live="polite">
        <div class="route-builder-loading__panel">
            <span class="route-builder-loading__spinner" aria-hidden="true"></span>
            <p class="eyebrow">Rota Viva em movimento</p>
            <strong>Adaptando sua rota por causa da chuva</strong>
            <span>Estamos preservando o que ainda funciona e buscando alternativas cobertas compatíveis com seu tempo, orçamento e perfil.</span>
        </div>
    </div>

@endsection
