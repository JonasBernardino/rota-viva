@extends('layouts.public')
@section('title', 'Painel municipal')
@section('content')
    <section class="admin-dashboard">

        <div class="admin-dashboard__heading">

            <div>
                <p class="eyebrow">
                    Inteligência territorial
                </p>

                <h1>
                    Visão da operação
                </h1>

                <p>
                    Acompanhe como visitantes estão
                    explorando o território e onde existem
                    oportunidades de melhoria na oferta.
                </p>
            </div>

            <form method="get" action="{{ route('admin.dashboard') }}" class="dashboard-period">
                <label for="period">
                    Período
                </label>

                <select id="period" name="period" onchange="this.form.submit()">
                    <option value="7" @selected($period === '7')>
                        Últimos 7 dias
                    </option>

                    <option value="30" @selected($period === '30')>
                        Últimos 30 dias
                    </option>

                    <option value="90" @selected($period === '90')>
                        Últimos 90 dias
                    </option>

                    <option value="all" @selected($period === 'all')>
                        Todo o período
                    </option>
                </select>
            </form>

        </div>


        {{-- =============================================
         INDICADORES
    ============================================== --}}

        <div class="dashboard-kpis">

            <article class="dashboard-kpi">
                <span>
                    Rotas criadas
                </span>

                <strong>
                    {{ number_format($overview['routesCreated'], 0, ',', '.') }}
                </strong>
            </article>


            <article class="dashboard-kpi">

                <span>
                    Demanda não atendida
                </span>

                <strong>
                    {{ number_format($overview['routesNotFound'], 0, ',', '.') }}
                </strong>

                <small>
                    Solicitações sem rota compatível
                </small>

            </article>


            <article class="dashboard-kpi">

                <span>
                    Rotas adaptadas
                </span>

                <strong>
                    {{ number_format($overview['adaptations'], 0, ',', '.') }}
                </strong>

                <small>
                    {{ $overview['adaptationRate'] }}%
                    das rotas
                </small>

            </article>


            <article class="dashboard-kpi">

                <span>
                    Custo médio estimado
                </span>

                <strong>
                    R$
                    {{ number_format($overview['averageCost'], 2, ',', '.') }}
                </strong>

            </article>


            <article class="dashboard-kpi">

                <span>
                    Duração média
                </span>

                <strong>
                    {{ round($overview['averageDuration'] / 60, 1) }}h
                </strong>

            </article>

        </div>


        {{-- =============================================
         COMPORTAMENTO
    ============================================== --}}

        <div class="dashboard-section-heading">

            <div>
                <p class="eyebrow">
                    Comportamento
                </p>

                <h2>
                    O que os visitantes procuram
                </h2>
            </div>

        </div>


        <div class="dashboard-two-columns">

            <article class="dashboard-panel">

                <h3>
                    Interesses mais buscados
                </h3>

                @forelse($interests as $item)
                    <div class="dashboard-ranking">

                        <div>
                            <span>
                                {{ $item['label'] }}
                            </span>

                            <strong>
                                {{ $item['percentage'] }}%
                            </strong>
                        </div>

                        <div class="dashboard-progress">

                            <span
                                style="
                                width:
                                {{ $item['percentage'] }}%
                            "></span>

                        </div>

                    </div>

                @empty

                    <p class="dashboard-empty">
                        Ainda não há dados suficientes.
                    </p>
                @endforelse

            </article>


            <article class="dashboard-panel">

                <h3>
                    Perfil das experiências
                </h3>

                @forelse($moods as $item)
                    <div class="dashboard-ranking">

                        <div>

                            <span>
                                {{ $item['label'] }}
                            </span>

                            <strong>
                                {{ $item['percentage'] }}%
                            </strong>

                        </div>

                        <div class="dashboard-progress">

                            <span
                                style="
                                width:
                                {{ $item['percentage'] }}%
                            "></span>

                        </div>

                    </div>

                @empty

                    <p class="dashboard-empty">
                        Ainda não há dados suficientes.
                    </p>
                @endforelse

            </article>

        </div>


        {{-- =============================================
         MAPA DE CALOR
    ============================================== --}}

        <div class="dashboard-section-heading">

            <div>
                <p class="eyebrow">
                    Território
                </p>

                <h2>
                    Onde está a demanda
                </h2>

                <p>
                    Visualize a concentração das experiências
                    geradas e como o fluxo muda após adaptações.
                </p>
            </div>

        </div>


        <article class="dashboard-panel dashboard-map-panel">

            <div class="dashboard-map-controls">

                <button type="button" class="dashboard-map-filter is-active" data-heatmap-type="demand">
                    Demanda das rotas
                </button>

                <button type="button" class="dashboard-map-filter" data-heatmap-type="added">
                    Adicionados em adaptações
                </button>

                <button type="button" class="dashboard-map-filter" data-heatmap-type="removed">
                    Removidos em adaptações
                </button>

            </div>


            <div class="dashboard-heatmap" data-dashboard-heatmap
                aria-label="
                Mapa de calor da demanda turística
            "></div>


            <script
            type="application/json"
            id="dashboard-heatmap-data"
        >@json($heatmap)</script>

        </article>


        {{-- =============================================
         TOP ATRATIVOS + DEMANDA NÃO ATENDIDA
    ============================================== --}}

        <div class="dashboard-two-columns">

            <article class="dashboard-panel">

                <p class="eyebrow">
                    Maior procura
                </p>

                <h3>
                    Atrativos mais recomendados
                </h3>

                <ol class="dashboard-top-list">

                    @forelse($topPlaces as $place)
                        <li>

                            <span>
                                {{ $place['name'] }}
                            </span>

                            <strong>
                                {{ $place['count'] }}
                                rotas
                            </strong>

                        </li>

                    @empty

                        <li>
                            Ainda não há rotas suficientes.
                        </li>
                    @endforelse

                </ol>

            </article>


            <article class="
                dashboard-panel
                dashboard-panel--opportunities
            ">

                <p class="eyebrow">
                    Oportunidades
                </p>

                <h3>
                    Demanda não atendida
                </h3>

                <p>
                    Preferências solicitadas pelos visitantes
                    para as quais não encontramos uma rota
                    compatível.
                </p>


                <div class="dashboard-opportunities">

                    @forelse($unmetDemand as $item)
                        <div class="dashboard-opportunity">

                            <div>

                                <strong>
                                    {{ $item['label'] }}
                                </strong>

                                <span>
                                    Demanda sem atendimento
                                </span>

                            </div>

                            <strong>
                                {{ $item['count'] }}
                            </strong>

                        </div>

                    @empty

                        <p class="dashboard-empty">
                            Nenhuma demanda não atendida
                            registrada neste período.
                        </p>
                    @endforelse

                </div>

            </article>

        </div>

    </section>
@endsection
