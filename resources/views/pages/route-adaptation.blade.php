@extends('layouts.public')

@section('title', $adaptation->title)

@section('description', $adaptation->summary)


{{-- =========================================================
     PREPARAÇÃO DOS DADOS DO MAPA

     Aqui transformamos os itens da adaptação em um JSON
     que será posteriormente lido pelo Leaflet.
========================================================= --}}
@php
    $adaptationMapStops = $adaptation->items
        ->map(function ($item) {
            return [
                'id' => $item->place->id,
                'name' => $item->place->name,
                'category' => $item->place->category?->name,
                'latitude' => $item->place->latitude,
                'longitude' => $item->place->longitude,
                'duration' => $item->duration_minutes,
                'cost' => $item->estimated_cost,
                'action' => $item->action,
                'position' => $item->position,
            ];
        })
        ->values();
@endphp


@section('content')

    {{-- =====================================================
         1. CABEÇALHO DA ROTA ADAPTADA
    ====================================================== --}}
    <section class="page-hero page-hero--compact">

        <div class="page-container page-hero__content">

            <p class="eyebrow">
                Rota adaptativa
            </p>

            <h1>
                {{ $adaptation->title }}
            </h1>

            <p>
                {{ $adaptation->summary }}
            </p>

            <div class="route-result-summary">

                <span>
                    ☂ Chuva
                </span>

                <span>
                    ◷
                    {{ $adaptation->total_duration_minutes }}
                    minutos
                </span>

                <span>
                    R$
                    {{ number_format($adaptation->total_estimated_cost, 2, ',', '.') }}
                </span>

            </div>

        </div>

    </section>


    {{-- =====================================================
         2. COMPARAÇÃO ANTES / DEPOIS
    ====================================================== --}}
    <section class="page-section">

        <div class="page-container">

            <div class="adaptation-comparison">

                {{-- =========================================
                     ROTA ORIGINAL
                ========================================== --}}
                <div class="adaptation-column">

                    <p class="eyebrow">
                        Antes
                    </p>

                    <h2>
                        Rota original
                    </h2>

                    @foreach ($itinerary->items as $item)
                        @php
                            $removed = $adaptation->items->first(
                                fn($adaptationItem) => $adaptationItem->place_id === $item->place_id &&
                                    $adaptationItem->action === 'REMOVED',
                            );
                        @endphp

                        <article class="adaptation-place
                            {{ $removed ? 'is-removed' : '' }}">

                            <span class="adaptation-place__status">

                                @if ($removed)
                                    Removido
                                @else
                                    Mantido
                                @endif

                            </span>

                            <h3>
                                {{ $item->place->name }}
                            </h3>

                            <p>
                                {{ $item->place->category?->name ?? 'Experiência' }}
                                ·
                                {{ $item->duration_minutes }} min
                            </p>

                            @if ($removed)
                                <p class="adaptation-place__reason">
                                    Atividade externa incompatível
                                    com a chuva.
                                </p>
                            @endif

                        </article>
                    @endforeach

                </div>


                {{-- =========================================
                     ROTA ADAPTADA
                ========================================== --}}
                <div class="adaptation-column">

                    <p class="eyebrow">
                        Agora
                    </p>

                    <h2>
                        Rota adaptada
                    </h2>

                    @foreach ($adaptation->items->where('action', '!=', 'REMOVED') as $item)
                        <article
                            class="adaptation-place
                            {{ $item->action === 'ADDED' ? 'is-added' : '' }}">

                            <span class="adaptation-place__status">

                                @if ($item->action === 'ADDED')
                                    Nova experiência
                                @else
                                    Mantido
                                @endif

                            </span>

                            <h3>
                                {{ $item->place->name }}
                            </h3>

                            <p>
                                {{ $item->place->category?->name ?? 'Experiência' }}
                                ·
                                {{ $item->duration_minutes }} min
                            </p>

                            <p class="adaptation-place__reason">
                                {{ $item->reason }}
                            </p>

                        </article>
                    @endforeach

                </div>

            </div>


            {{-- =================================================
                 3. MAPA DA NOVA ROTA

                 ESSA É A PARTE NOVA DO LEAFLET.
                 Ela fica DEPOIS da comparação Antes/Agora.
            ================================================== --}}
            <section class="adaptation-map-section" aria-labelledby="adaptation-map-title">

                <div class="adaptation-map-heading">

                    <div>

                        <p class="eyebrow">
                            Percurso atualizado
                        </p>

                        <h2 id="adaptation-map-title">
                            Veja como sua rota mudou
                        </h2>

                        <p>
                            As experiências incompatíveis foram
                            substituídas sem refazer toda a jornada.
                        </p>

                    </div>


                    {{-- LEGENDA DO MAPA --}}
                    <div class="route-map-legend">

                        <span>
                            <i
                                class="
                                    route-map-legend__dot
                                    route-map-legend__dot--kept
                                "></i>

                            Mantido
                        </span>

                        <span>
                            <i
                                class="
                                    route-map-legend__dot
                                    route-map-legend__dot--added
                                "></i>

                            Nova experiência
                        </span>

                        <span>
                            <i
                                class="
                                    route-map-legend__dot
                                    route-map-legend__dot--removed
                                "></i>

                            Removido
                        </span>

                    </div>

                </div>


                {{-- =============================================
                     O LEAFLET VAI SER RENDERIZADO NESTA DIV
                ============================================== --}}
                <div class="route-map-wrapper">

                    <div id="adaptation-route-map" class="route-map-container route-map-container--large" data-route-map
                        data-map-type="adaptation" data-stops='@json($adaptationMapStops)'
                        aria-label="Mapa da rota adaptada"></div>

                    <div class="route-map-location-warning" data-location-warning hidden role="status">
                        <span aria-hidden="true">⌖</span>

                        <div>
                            <strong>
                                Não conseguimos acessar sua localização.
                            </strong>

                            <p>
                                O mapa da rota adaptada continua disponível.
                            </p>
                        </div>
                    </div>

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
                                Sua rota adaptada continua disponível
                                logo acima.
                            </p>
                        </div>
                    </div>

                </div>

            </section>


            {{-- =================================================
                 4. MENSAGEM FINAL
            ================================================== --}}
            <section class="adaptation-success">

                <span class="adaptation-success__icon">
                    ✓
                </span>

                <div>

                    <p class="eyebrow">
                        Experiência preservada
                    </p>

                    <h2>
                        Sua rota continua fazendo sentido.
                    </h2>

                    <p>
                        Ajustamos somente as experiências
                        impactadas pela chuva e mantivemos
                        o restante do percurso.
                    </p>

                </div>

            </section>

        </div>

    </section>

@endsection
