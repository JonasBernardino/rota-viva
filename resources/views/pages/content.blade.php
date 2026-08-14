@extends('layouts.public')
@section('title', $title)
@section('description', $description)
@section('content')
    <section class="page-hero page-hero--compact"><div class="page-container page-hero__content"><p class="eyebrow">Rota Viva</p><h1>{{ $title }}</h1><p>{{ $description }}</p></div></section>
    <section class="page-section"><article class="page-container editorial-content"><h2>Conteúdo em construção</h2><p>A página já faz parte da navegação oficial do portal. O conteúdo definitivo será produzido e validado junto à gestão municipal nas próximas etapas.</p><a class="text-link" href="{{ route('home') }}">← Voltar para a página inicial</a></article></section>
@endsection
