@extends('layouts.public')
@section('title', 'Criar minha rota')
@section('description', 'Conte como deseja viver a cidade e receba uma rota personalizada.')
@section('content')
    <section class="page-hero page-hero--compact"><div class="page-container page-hero__content"><p class="eyebrow">Rota adaptativa</p><h1>Como você quer viver a cidade hoje?</h1><p>Esta será a jornada de preferências que alimentará o motor de rotas.</p></div></section>
    <section class="page-section"><div class="page-container route-builder-card">
        <ol class="route-builder-steps"><li><span>01</span>Seu momento</li><li><span>02</span>Tempo e orçamento</li><li><span>03</span>Companhia e mobilidade</li><li><span>04</span>Sua experiência</li></ol>
        <form action="{{ route('routes.create') }}" method="get"><label for="experience-query">Conte o que você procura</label><textarea id="experience-query" name="q" rows="5" placeholder="Ex.: Quero cultura e tranquilidade, estou com uma criança e tenho quatro horas.">{{ request('q') }}</textarea><button class="route-cta" type="submit">Continuar</button></form>
    </div></section>
@endsection
