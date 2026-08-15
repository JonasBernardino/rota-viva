@extends('layouts.public')

@section('title', 'Painel de Gestão Municipal — Rota Viva')

@section('content')

    <style>
        /*
        |--------------------------------------------------------------------------
        | Controles do cabeçalho do Dashboard
        |--------------------------------------------------------------------------
        */

        .admin-dashboard-header {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .admin-dashboard-header__actions {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            width: 100%;
            max-width: 760px;
        }

        .dashboard-period {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex: 1;
            margin: 0;
        }

        .dashboard-period label {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .dashboard-period__select-wrapper {
            position: relative;
            width: 100%;
        }

        .dashboard-period__select-wrapper::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 1.15rem;
            width: 8px;
            height: 8px;

            border-right: 2px solid #164b3f;
            border-bottom: 2px solid #164b3f;

            transform: translateY(-65%) rotate(45deg);

            pointer-events: none;
        }

        .dashboard-period select {
            appearance: none;
            -webkit-appearance: none;

            width: 100%;
            height: 52px;

            padding: 0 3rem 0 1.1rem;

            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 12px;

            background: rgba(255, 255, 255, 0.96);

            color: #163d35;

            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 600;

            cursor: pointer;

            outline: none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                transform 0.2s ease;
        }

        .dashboard-period select:hover {
            border-color: rgba(255, 255, 255, 0.7);
        }

        .dashboard-period select:focus {
            border-color: #e16446;

            box-shadow:
                0 0 0 3px rgba(225, 100, 70, 0.18);
        }

        .admin-dashboard-logout-form {
            margin: 0;
            flex: 0 0 auto;
        }

        .admin-logout-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;

            height: 52px;

            padding: 0 1.4rem;

            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 12px;

            background: rgba(5, 54, 45, 0.35);

            color: #fff;

            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 700;

            white-space: nowrap;

            cursor: pointer;

            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);

            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .admin-logout-button svg {
            width: 18px;
            height: 18px;

            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .admin-logout-button:hover {
            background: rgba(225, 100, 70, 0.92);
            border-color: #e16446;

            transform: translateY(-1px);

            box-shadow:
                0 8px 24px rgba(0, 0, 0, 0.16);
        }

        .admin-logout-button:focus-visible {
            outline: 3px solid rgba(255, 255, 255, 0.45);
            outline-offset: 3px;
        }

        @media (max-width: 767px) {
            .admin-dashboard-header__actions {
                flex-direction: column;
                align-items: stretch;

                max-width: none;
            }

            .admin-dashboard-logout-form {
                width: 100%;
            }

            .admin-logout-button {
                width: 100%;
            }
        }
    </style>


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


                {{-- =====================================================
                     FILTROS E AÇÕES
                ====================================================== --}}

                <div class="admin-dashboard-header__actions">

                    <a
                        href="{{ route('admin.appearance.edit') }}"
                        class="admin-logout-button"
                        style="text-decoration: none;"
                    >
                        <span>
                            Aparência da cidade
                        </span>
                    </a>

                    <form
                        method="get"
                        action="{{ route('admin.dashboard') }}"
                        class="dashboard-period"
                    >

                        <label for="period">
                            Período
                        </label>

                        <div class="dashboard-period__select-wrapper">

                            <select
                                id="period"
                                name="period"
                                onchange="this.form.submit()"
                                aria-label="Selecionar período do dashboard"
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

                        </div>

                    </form>


                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                        class="admin-dashboard-logout-form"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="admin-logout-button"
                        >

                            <svg
                                aria-hidden="true"
                                viewBox="0 0 24 24"
                            >
                                <path d="M10 5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4"></path>
                                <path d="M14 8l4 4-4 4"></path>
                                <path d="M9 12h9"></path>
                            </svg>

                            <span>
                                Encerrar sessão
                            </span>

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

                                        {{ $place['count'] === 1
                                            ? 'rota'
                                            : 'rotas'
                                        }}
                                    </strong>

                                </li>

                            @empty

                                <li class="dashboard-empty">
                                    Ainda não há rotas suficientes.
                                </li>

                            @endforelse

                        </ol>

                    </article>


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
