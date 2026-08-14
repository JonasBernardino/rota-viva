@extends('layouts.public')

@section('title', $adaptation->title)

@section('description', $adaptation->summary)

@section('content')

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
                {{ number_format(
                    $adaptation->total_estimated_cost,
                    2,
                    ',',
                    '.'
                ) }}
            </span>

        </div>

    </div>

</section>

<section class="page-section">

    <div class="page-container">

        <div class="adaptation-comparison">

            {{-- ANTES --}}
            <div class="adaptation-column">

                <p class="eyebrow">
                    Antes
                </p>

                <h2>
                    Rota original
                </h2>

                @foreach(
                    $itinerary->items
                    as $item
                )

                    @php
                        $removed =
                            $adaptation
                                ->items
                                ->first(
                                    fn ($adaptationItem) =>
                                        $adaptationItem->place_id
                                            === $item->place_id
                                        && $adaptationItem->action
                                            === 'REMOVED'
                                );
                    @endphp

                    <article
                        class="adaptation-place
                        {{ $removed ? 'is-removed' : '' }}"
                    >

                        <span class="adaptation-place__status">

                            @if($removed)
                                Removido
                            @else
                                Mantido
                            @endif

                        </span>

                        <h3>
                            {{ $item->place->name }}
                        </h3>

                        <p>
                            {{ $item->place->category->name }}
                            ·
                            {{ $item->duration_minutes }} min
                        </p>

                        @if($removed)

                            <p class="adaptation-place__reason">
                                Atividade externa incompatível
                                com a chuva.
                            </p>

                        @endif

                    </article>

                @endforeach

            </div>

            {{-- DEPOIS --}}
            <div class="adaptation-column">

                <p class="eyebrow">
                    Agora
                </p>

                <h2>
                    Rota adaptada
                </h2>

                @foreach(
                    $adaptation->items
                        ->where('action', '!=', 'REMOVED')
                    as $item
                )

                    <article
                        class="adaptation-place
                        {{ $item->action === 'ADDED'
                            ? 'is-added'
                            : '' }}"
                    >

                        <span class="adaptation-place__status">

                            @if(
                                $item->action
                                    === 'ADDED'
                            )

                                Nova experiência

                            @else

                                Mantido

                            @endif

                        </span>

                        <h3>
                            {{ $item->place->name }}
                        </h3>

                        <p>
                            {{ $item->place->category->name }}
                            ·
                            {{ $item->duration_minutes }}
                            min
                        </p>

                        <p class="adaptation-place__reason">
                            {{ $item->reason }}
                        </p>

                    </article>

                @endforeach

            </div>

        </div>

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