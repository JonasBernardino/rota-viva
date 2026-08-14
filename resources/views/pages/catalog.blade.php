@extends('layouts.public')
@section('title', $title)
@section('description', $description)
@section('content')
    <section class="page-hero page-hero--compact"><div class="page-container page-hero__content"><p class="eyebrow">{{ $eyebrow }}</p><h1>{{ $title }}</h1><p>{{ $description }}</p></div></section>
    <section class="page-section"><div class="page-container">
        <div class="catalog-toolbar" aria-label="Filtros provisórios"><strong>Explore o catálogo</strong><span>Filtros e busca serão adicionados na próxima etapa.</span></div>
        <div class="catalog-grid">
            @foreach ($items as $item)
                @php($slug = str($item)->slug())
                <article class="catalog-card"><div class="catalog-card__placeholder" aria-hidden="true"><span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div><div class="catalog-card__content"><p class="eyebrow">Informação validada</p><h2>{{ $item }}</h2><p>Esta página já está preparada para receber os dados oficiais do município.</p><a class="text-link" href="{{ route($routePrefix.'.show', $slug) }}">Ver detalhes <span aria-hidden="true">→</span></a></div></article>
            @endforeach
        </div>
    </div></section>
@endsection
