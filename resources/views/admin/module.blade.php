@extends('layouts.public')
@section('title', $title)
@section('content')
    <section class="page-hero page-hero--compact"><div class="page-container page-hero__content"><p class="eyebrow">Gestão municipal</p><h1>{{ $title }}</h1><p>Estrutura inicial para cadastro, validação e publicação deste conteúdo.</p></div></section>
    <section class="page-section"><div class="page-container admin-placeholder"><div><strong>Nenhum registro carregado</strong><p>O CRUD deste módulo será implementado em uma etapa própria.</p></div><button class="route-cta" type="button" disabled>Novo cadastro</button></div></section>
@endsection
