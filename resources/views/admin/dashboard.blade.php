@extends('layouts.public')
@section('title', 'Painel de Gestão Municipal — Rota Viva')

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p class="eyebrow">Gestão Municipal · {{ $currentTenant->name ?? 'Lucena' }}</p>
                    <h1>Painel Rota Viva</h1>
                    <p>Visão geral de atrativos validados, trade turístico e indicadores territoriais.</p>
                </div>
                <div>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="text-link" style="background: transparent; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; cursor: pointer; color: #dc2626;">
                            Encerrar Sessão
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container">
            {{-- Indicadores Numéricos --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 32px;">
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Atrativos Cadastrados</span>
                    <h2 style="font-size: 2rem; color: #0284c7; margin: 6px 0 0 0; font-weight: 700;">{{ $stats['places_count'] }}</h2>
                </div>

                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Trade & Estabelecimentos</span>
                    <h2 style="font-size: 2rem; color: #0f172a; margin: 6px 0 0 0; font-weight: 700;">{{ $stats['businesses_count'] }}</h2>
                </div>

                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Com Selo de Qualidade</span>
                    <h2 style="font-size: 2rem; color: #16a34a; margin: 6px 0 0 0; font-weight: 700;">{{ $stats['validated_businesses_count'] }}</h2>
                </div>

                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Eventos na Agenda</span>
                    <h2 style="font-size: 2rem; color: #f59e0b; margin: 6px 0 0 0; font-weight: 700;">{{ $stats['events_count'] }}</h2>
                </div>
            </div>

            {{-- Módulos Administrativos --}}
            <h2 style="font-size: 1.25rem; color: #0f172a; margin-bottom: 16px;">Módulos de Gestão do Território</h2>
            <div class="admin-module-grid">
                @foreach ([
                    ['admin.tourist-spots.index', 'Pontos turísticos', 'Atrativos naturais e praias'],
                    ['admin.culture.index', 'História e cultura', 'Patrimônio histórico e igrejas'],
                    ['admin.establishments.index', 'Estabelecimentos', 'Hospedagens e gastronomia'],
                    ['admin.tours.index', 'Passeios', 'Atividades e vivências'],
                    ['admin.guides.index', 'Guias turísticos', 'Condutores cadastrados'],
                    ['admin.events.index', 'Eventos', 'Agenda cultural municipal'],
                    ['admin.official-itineraries.index', 'Roteiros oficiais', 'Curadoria da secretaria'],
                ] as [$routeName, $label, $desc])
                    <a class="admin-module-card" href="{{ route($routeName) }}">
                        <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <strong>{{ $label }}</strong>
                        <p style="font-size: 0.85rem; color: #64748b; margin: 4px 0 8px 0;">{{ $desc }}</p>
                        <small>Acessar módulo →</small>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
