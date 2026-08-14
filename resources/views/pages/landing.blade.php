@extends('layouts.public')
@section('title', $title)
@section('description', $description)
@section('content')
    <section class="page-hero"><div class="page-container page-hero__content"><p class="eyebrow">{{ $eyebrow }}</p><h1>{{ $title }}</h1><p>{{ $description }}</p></div></section>
    <section class="page-section"><div class="page-container link-card-grid">
        @foreach ($links as $link)
            <a class="link-card" href="{{ route($link['route']) }}"><span class="link-card__number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h2>{{ $link['title'] }}</h2><p>{{ $link['description'] }}</p><span class="text-link">Explorar <span aria-hidden="true">→</span></span></a>
        @endforeach
    </div></section>
@endsection
