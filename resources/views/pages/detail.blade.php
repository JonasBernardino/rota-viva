@extends('layouts.public')
@php($itemTitle = str($slug)->replace('-', ' ')->title())
@section('title', $itemTitle)
@section('content')
    <section class="page-hero page-hero--detail"><div class="page-container page-hero__content"><p class="eyebrow">{{ $catalogTitle }}</p><h1>{{ $itemTitle }}</h1><p>{{ $catalogDescription }}</p></div></section>
    <section class="page-section"><div class="page-container detail-layout"><div class="detail-placeholder" aria-label="Espaço reservado para a galeria"></div><article class="detail-copy"><p class="eyebrow">Conteúdo em preparação</p><h2>Informações oficiais em breve</h2><p>Esta estrutura receberá descrição, horários, localização, acessibilidade, custos, contatos e demais informações específicas deste conteúdo.</p><a class="text-link" href="{{ route($routePrefix.'.index') }}">← Voltar para {{ str($catalogTitle)->lower() }}</a></article></div></section>
@endsection
