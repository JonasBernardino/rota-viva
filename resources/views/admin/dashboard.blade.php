@extends('layouts.public')
@section('title', 'Painel municipal')
@section('content')
    <section class="page-hero page-hero--compact"><div class="page-container page-hero__content"><p class="eyebrow">Gestão municipal</p><h1>Painel Rota Viva</h1><p>Gerencie o conteúdo oficial que alimentará o portal e as rotas adaptativas.</p></div></section>
    <section class="page-section"><div class="page-container admin-module-grid">
        @foreach ([
            ['admin.tourist-spots.index', 'Pontos turísticos'], ['admin.culture.index', 'História e cultura'], ['admin.establishments.index', 'Estabelecimentos'], ['admin.tours.index', 'Passeios'], ['admin.guides.index', 'Guias turísticos'], ['admin.events.index', 'Eventos'], ['admin.official-itineraries.index', 'Roteiros oficiais'],
        ] as [$routeName, $label])
            <a class="admin-module-card" href="{{ route($routeName) }}"><span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $label }}</strong><small>Gerenciar conteúdo →</small></a>
        @endforeach
    </div></section>
@endsection
