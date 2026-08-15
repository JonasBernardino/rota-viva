@extends('layouts.public')

@section('title', 'Painel de Gestão Municipal — Rota Viva')

@section('content')

    {{-- =========================================================
         CABEÇALHO
    ========================================================== --}}
    <section class="page-hero page-hero--compact">

        <div class="page-container page-hero__content">

            <div class="admin-dashboard-header">

                <div>
                    <p class="eyebrow">
                        Gestão Municipal ·
                        {{ $currentTenant->name ?? 'Município' }}
                    </p>

                    <h1>
                        Painel Rota Viva
                    </h1>

                    <p>
                        Acompanhe a operação, o comportamento dos visitantes,
                        a distribuição da demanda turística e oportunidades
                        de melhoria na oferta do território.
                    </p>
                </div>

                <div class="admin-dashboard-header__actions">

                    <form
                        method="get"
                        action="{{ route('admin.dashboard') }}"
                        class="dashboard-period"
                    >
                        <label for="period">
                            Período
                        </label>

                        <select
                            id="period"
                            name="period"
                            onchange="this.form.submit()"
                        >
                            <option
                                value="7"
                                @selected($period === '7')
                            >
                                Últimos 7 dias
                            </option>

                            <option
                                value="30"
                                @selected($period === '30')
                            >
                                Últimos 30 dias
                            </option>

                            <option
                                value="90"
                                @selected($period === '90')
                            >
                                Últimos 90 dias
                            </option>

                            <option
                                value="all"
                                @selected($period === 'all')
                            >
                                Todo o período
                            </option>
                        </select>
                    </form>

                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="admin-logout-button"
                        >
                            Encerrar sessão
                        </button>
                    </form>

                </div>

            </div>

        </div>

    </section>


    <section class="page-section">

        <div class="page-container">

            <div class="admin-dashboard">


                {{-- =====================================================
                     VISÃO DA OPERAÇÃO
                ====================================================== --}}

                <div class="dashboard-section-heading">

                    <div>
                        <p class="eyebrow">
                            Operação
                        </p>

                        <h2>
                            Visão geral das jornadas
                        </h2>

                        <p>
                            Indicadores calculados a partir das rotas
                            realmente solicitadas pelos visitantes.
                        </p>
                    </div>

                </div>


                <div class="dashboard-kpis">

                    {{-- Rotas criadas --}}
                    <article class="dashboard-kpi">

                        <span>
                            Rotas criadas
                        </span>

                        <strong>
                            {{ number_format(
                                $overview['routesCreated'],
                                0,
                                ',',
                                '.'
                            ) }}
                        </strong>

                        <small>
                            Jornadas atendidas
                        </small>

                    </article>


                    {{-- Demanda não atendida --}}
                    <article class="dashboard-kpi dashboard-kpi--attention">

                        <span>
                            Demanda não atendida
                        </span>

                        <strong>
                            {{ number_format(
                                $overview['routesNotFound'],
                                0,
                                ',',
                                '.'
                            ) }}
                        </strong>

                        <small>
                            Solicitações sem rota compatível
                        </small>

                    </article>


                    {{-- Rotas adaptadas --}}
                    <article class="dashboard-kpi">

                        <span>
                            Rotas adaptadas
                        </span>

                        <strong>
                            {{ number_format(
                                $overview['adaptations'],
                                0,
                                ',',
                                '.'
                            ) }}
                        </strong>

                        <small>
                            {{ $overview['adaptationRate'] }}%
                            das rotas geradas
                        </small>

                    </article>


                    {{-- Custo médio --}}
                    <article class="dashboard-kpi">

                        <span>
                            Custo médio estimado
                        </span>

                        <strong>
                            R$
                            {{ number_format(
                                $overview['averageCost'],
                                2,
                                ',',
                                '.'
                            ) }}
                        </strong>

                        <small>
                            Por roteiro
                        </small>

                    </article>


                    {{-- Duração média --}}
                    <article class="dashboard-kpi">

                        <span>
                            Duração média
                        </span>

                        <strong>
                            {{ round(
                                $overview['averageDuration'] / 60,
                                1
                            ) }}h
                        </strong>

                        <small>
                            Tempo médio das experiências
                        </small>

                    </article>

                </div>


                {{-- =====================================================
                     COMPORTAMENTO DOS VISITANTES
                ====================================================== --}}

                <div class="dashboard-section-heading">

                    <div>
                        <p class="eyebrow">
                            Comportamento
                        </p>

                        <h2>
                            O que os visitantes procuram
                        </h2>

                        <p>
                            Preferências identificadas a partir das
                            solicitações em linguagem natural.
                        </p>
                    </div>

                </div>


                <div class="dashboard-two-columns">

                    {{-- Interesses --}}
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
                                        style="width: {{ $item['percentage'] }}%"
                                    ></span>

                                </div>

                            </div>

                        @empty

                            <p class="dashboard-empty">
                                Ainda não há dados suficientes
                                para identificar os interesses predominantes.
                            </p>

                        @endforelse

                    </article>


                    {{-- Perfil/moods --}}
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
                                        style="width: {{ $item['percentage'] }}%"
                                    ></span>

                                </div>

                            </div>

                        @empty

                            <p class="dashboard-empty">
                                Ainda não há dados suficientes
                                para identificar os perfis predominantes.
                            </p>

                        @endforelse

                    </article>

                </div>


                {{-- =====================================================
                     MAPA DE CALOR
                ====================================================== --}}

                <div class="dashboard-section-heading">

                    <div>

                        <p class="eyebrow">
                            Território
                        </p>

                        <h2>
                            Onde está a demanda
                        </h2>

                        <p>
                            Visualize onde as rotas estão concentrando
                            visitantes e como a distribuição muda após
                            adaptações.
                        </p>

                    </div>

                </div>


                <article class="dashboard-panel dashboard-map-panel">

                    <div class="dashboard-map-controls">

                        <button
                            type="button"
                            class="dashboard-map-filter is-active"
                            data-heatmap-type="demand"
                        >
                            Demanda das rotas
                        </button>

                        <button
                            type="button"
                            class="dashboard-map-filter"
                            data-heatmap-type="added"
                        >
                            Adicionados em adaptações
                        </button>

                        <button
                            type="button"
                            class="dashboard-map-filter"
                            data-heatmap-type="removed"
                        >
                            Removidos em adaptações
                        </button>

                    </div>


                    <div
                        class="dashboard-heatmap"
                        data-dashboard-heatmap
                        aria-label="Mapa de calor da demanda turística"
                    ></div>


                    <script
                        type="application/json"
                        id="dashboard-heatmap-data"
                    >@json($heatmap)</script>

                </article>


                {{-- =====================================================
                     ATRATIVOS + DEMANDA NÃO ATENDIDA
                ====================================================== --}}

                <div class="dashboard-two-columns">

                    {{-- Top atrativos --}}
                    <article class="dashboard-panel">

                        <p class="eyebrow">
                            Maior procura
                        </p>

                        <h3>
                            Atrativos mais recomendados
                        </h3>

                        <p>
                            Locais presentes com maior frequência
                            nos roteiros gerados.
                        </p>

                        <ol class="dashboard-top-list">

                            @forelse($topPlaces as $place)

                                <li>

                                    <span>
                                        {{ $place['name'] }}
                                    </span>

                                    <strong>
                                        {{ $place['count'] }}
                                        {{ $place['count'] === 1 ? 'rota' : 'rotas' }}
                                    </strong>

                                </li>

                            @empty

                                <li class="dashboard-empty">
                                    Ainda não há rotas suficientes.
                                </li>

                            @endforelse

                        </ol>

                    </article>


                    {{-- Demanda não atendida --}}
                    <article
                        class="
                            dashboard-panel
                            dashboard-panel--opportunities
                        "
                    >

                        <p class="eyebrow">
                            Oportunidades
                        </p>

                        <h3>
                            Demanda não atendida
                        </h3>

                        <p>
                            Preferências solicitadas pelos visitantes
                            para as quais o território ainda não ofereceu
                            uma rota compatível.
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


                {{-- =====================================================
                     GESTÃO DO TERRITÓRIO
                ====================================================== --}}

                <div class="dashboard-section-heading">

                    <div>

                        <p class="eyebrow">
                            Administração
                        </p>

                        <h2>
                            Gestão do território
                        </h2>

                        <p>
                            Atualize atrativos, estabelecimentos,
                            experiências e demais informações utilizadas
                            pelo Rota Viva.
                        </p>

                    </div>

                </div>


                <div class="admin-module-grid">

                    @foreach ([
                        [
                            'admin.tourist-spots.index',
                            'Pontos turísticos',
                            'Atrativos naturais, monumentos e praias',
                        ],

                        [
                            'admin.culture.index',
                            'História e cultura',
                            'Patrimônio histórico, tradições e memória',
                        ],

                        [
                            'admin.establishments.index',
                            'Estabelecimentos',
                            'Hospedagens, gastronomia e comércio local',
                        ],

                        [
                            'admin.tours.index',
                            'Passeios',
                            'Atividades, experiências e vivências',
                        ],

                        [
                            'admin.guides.index',
                            'Guias turísticos',
                            'Condutores e profissionais cadastrados',
                        ],

                        [
                            'admin.events.index',
                            'Eventos',
                            'Agenda cultural e turística municipal',
                        ],

                        [
                            'admin.official-itineraries.index',
                            'Roteiros oficiais',
                            'Curadoria e percursos da gestão municipal',
                        ],
                    ] as [$routeName, $label, $desc])

                        <a
                            class="admin-module-card"
                            href="{{ route($routeName) }}"
                        >

                            <span>
                                {{ str_pad(
                                    (string) $loop->iteration,
                                    2,
                                    '0',
                                    STR_PAD_LEFT
                                ) }}
                            </span>

                            <strong>
                                {{ $label }}
                            </strong>

                            <p>
                                {{ $desc }}
                            </p>

                            <small>
                                Acessar módulo →
                            </small>

                        </a>

                    @endforeach

                </div>

            </div>

        </div>

    </section>

@endsection