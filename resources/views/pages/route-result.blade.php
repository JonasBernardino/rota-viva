@extends('layouts.public')

@php
    $mapStops = $itinerary->items
        ->map(function ($item) {
            return [
                'id' => $item->place->id,
                'name' => $item->place->name,
                'category' => $item->place->category?->name,
                'latitude' => $item->place->latitude,
                'longitude' => $item->place->longitude,
                'duration' => $item->duration_minutes,
                'cost' => $item->estimated_cost,
            ];
        })
        ->values();
@endphp

@section('title', $itinerary->title)

@section('description', $itinerary->summary)

@section('content')

    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">

            <p class="eyebrow">
                Sua rota
            </p>

            <h1>
                {{ $itinerary->title }}
            </h1>

            <p>
                {{ $itinerary->summary }}
            </p>

            <div class="route-result-summary">

                <span>
                    ◷
                    {{ $itinerary->total_duration_minutes }}
                    minutos
                </span>

                <span>
                    R$
                    {{ number_format($itinerary->total_estimated_cost, 2, ',', '.') }}
                </span>

                <span>
                    {{ $itinerary->items->count() }}
                    paradas
                </span>

            </div>

        </div>
    </section>

    <section class="page-section">

        <div class="page-container">

            <div class="route-result-layout">

                <div class="route-timeline">

                    @foreach ($itinerary->items as $item)
                        <article class="route-stop">

                            <div class="route-stop__number">
                                {{ str_pad($item->position, 2, '0', STR_PAD_LEFT) }}
                            </div>

                            <div class="route-stop__content">

                                <p class="eyebrow">
                                    {{ $item->place->category->name }}
                                </p>

                                <h2>
                                    {{ $item->place->name }}
                                </h2>

                                <div class="route-stop__meta">

                                    <span>
                                        ◷
                                        {{ $item->duration_minutes }}
                                        min
                                    </span>

                                    <span>
                                        R$
                                        {{ number_format($item->estimated_cost, 2, ',', '.') }}
                                    </span>

                                </div>

                                <p>
                                    {{ $item->reason }}
                                </p>

                            </div>

                        </article>
                    @endforeach

                </div>

                <aside class="route-result-map">

                    <div class="route-map-wrapper">

                        <div id="route-map" class="route-map-container" data-route-map data-map-type="route"
                            data-stops='@json($mapStops)' aria-label="Mapa das paradas da sua rota"></div>

                        <div class="route-map-location-warning" data-location-warning hidden role="status">
                            <span aria-hidden="true">⌖</span>

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
                                    para abrir o Google Maps ou Waze.
                                </p>
                            </div>
                        </div>

                    </div>

                </aside>

            </div>

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

                <form action="{{ route('routes.adapt.rain', $itinerary) }}" method="post">
                    @csrf

                    <button class="route-cta" type="submit">
                        ☂ Começou a chover
                    </button>
                </form>

            </div>

        </div>

    </section>

@endsection
