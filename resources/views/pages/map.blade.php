@extends('layouts.public')
@section('title', 'Mapa da cidade')
@section('description', 'Visualize atrativos, serviços e experiências no território.')
@section('content')
    <section class="page-hero page-hero--compact"><div class="page-container page-hero__content"><p class="eyebrow">Explore por localização</p><h1>Mapa da cidade</h1><p>O mapa interativo reunirá atrativos, passeios, hospedagens, gastronomia e eventos.</p></div></section>
    <section class="page-section"><div class="page-container"><div class="map-placeholder"><span>Mapa Leaflet + OpenStreetMap</span><p>A camada cartográfica será conectada quando os locais oficiais estiverem cadastrados.</p></div></div></section>
@endsection
