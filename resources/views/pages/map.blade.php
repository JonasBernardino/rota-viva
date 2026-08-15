@extends('layouts.public')
@section('title', 'Mapa da Cidade — Rota Viva')
@section('description', 'Visualize atrativos, patrimônios históricos e experiências no território.')

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">Cartografia e Território</p>
            <h1>Mapa da Cidade</h1>
            <p>Explore a distribuição geográfica dos {{ count($places) }} atrativos oficiais validados de {{ $currentTenant->nome ?? 'Município' }}.</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container">
            <div class="route-map-wrapper" style="margin-bottom: 32px;">
                <div
                    id="route-map"
                    class="route-map-container route-map-container--large"
                    data-route-map
                    data-map-type="route"
                    data-stops='@json($mapStops)'
                    aria-label="Mapa interativo dos pontos turísticos oficiais de {{ $currentTenant->nome ?? 'Município' }}"
                    style="min-height: 480px; width: 100%; border-radius: 12px; border: 1px solid #cbd5e1;"
                ></div>

                <div class="route-map-location-warning" data-location-warning hidden role="status">
                    <span aria-hidden="true">⌖</span>
                    <div>
                        <strong>Não conseguimos acessar sua localização.</strong>
                        <p>Você ainda pode visualizar normalmente todos os pontos no mapa.</p>
                    </div>
                </div>
            </div>

            {{-- Lista dos Pontos Abaixo do Mapa --}}
            <div class="section-heading" style="margin-bottom: 20px;">
                <h2 style="font-size: 1.5rem; color: #0f172a;">Atrativos no Mapa</h2>
            </div>

            <div class="catalog-grid">
                @foreach ($places as $place)
                    @php
                        $media = $place->midias->firstWhere('is_destaque', true) ?? $place->midias->first();
                        $imagePath = $media?->url;
                        $imageUrl = $imagePath
                            ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://', '/']) ? $imagePath : asset('storage/'.$imagePath))
                            : null;
                    @endphp

                    <article class="catalog-card">
                        <div class="catalog-card__media {{ $imageUrl ? '' : 'catalog-card__media--placeholder' }}">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $media?->descricao_acessibilidade ?: $place->nome }}">
                            @else
                                <span aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            @endif
                        </div>
                        <div class="catalog-card__content">
                            <p class="eyebrow">{{ $place->category->name ?? 'Atrativo' }}</p>
                            <h2>{{ $place->name }}</h2>
                            <p>{{ Str::limit($place->description, 110) }}</p>
                            <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 12px;">
                                <span>📍 {{ $place->latitude }}, {{ $place->longitude }}</span>
                            </div>
                            <a class="text-link" href="{{ route('tourist-spots.show', $place->slug) }}">
                                Ver detalhes <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
