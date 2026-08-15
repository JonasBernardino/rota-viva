@extends('layouts.public')

@php
    $name = $item->name ?? $item->title ?? str($slug)->replace('-', ' ')->title();
    $desc = $item->description ?? $item->summary ?? 'Informações oficiais validadas pelo município.';
    $categoryName = $item->category->name ?? (is_string($item->category ?? null) ? ucfirst($item->category) : null);
    $municipalityName = $currentTenant->nome ?? 'Município';
    $municipalityState = $currentTenant->uf ?? null;
    $locationFallback = $municipalityState ? $municipalityName.' — '.$municipalityState : $municipalityName;
    $location = $item->address ? $item->address . ($item->neighborhood ? ' — ' . $item->neighborhood : '') : ($item->location_name ?? $item->neighborhood ?? $locationFallback);
    $mediaItems = collect($item->midias ?? []);
    if ($mediaItems->isEmpty() && isset($item->itens)) {
        $mediaItems = collect($item->itens)
            ->flatMap(fn ($routeItem) => $routeItem->atrativo?->midias ?? collect())
            ->values();
    }
    $singleImage = $item->imagem_capa ?? $item->imagem_url ?? null;
    $singleImageUrl = $singleImage
        ? (\Illuminate\Support\Str::startsWith($singleImage, ['http://', 'https://', '/']) ? $singleImage : asset('storage/'.$singleImage))
        : null;
    $fallbackImage = asset('images/rota-viva-hero.webp');
@endphp

@section('title', $name . ' — ' . $catalogTitle)
@section('description', Str::limit($desc, 150))

@section('content')
    <section class="page-hero page-hero--detail">
        <div class="page-container page-hero__content">
            <p class="eyebrow">
                <a href="{{ route($routePrefix.'.index') }}" style="color: inherit; text-decoration: none;">← {{ $catalogTitle }}</a>
                @if ($categoryName)
                    <span style="opacity: 0.6;">·</span> {{ $categoryName }}
                @endif
            </p>

            <h1>{{ $name }}</h1>
            <p>{{ $catalogDescription }}</p>

            @if (!empty($item->has_seal_of_quality))
                <div style="margin-top: 12px;">
                    <span style="display: inline-flex; align-items: center; gap: 6px; background-color: #fef3c7; color: #92400e; font-size: 0.85rem; padding: 4px 12px; border-radius: 9999px; font-weight: 600;">
                        ★ Estabelecimento com Selo de Qualidade Municipal
                    </span>
                </div>
            @endif
        </div>
    </section>

    <section class="page-section">
        <div class="page-container detail-layout">
            @if ($mediaItems->isNotEmpty())
                <div class="detail-gallery" aria-label="Fotos de {{ $name }}">
                    @foreach ($mediaItems as $media)
                        @php
                            $mediaUrl = $media->url;
                            $imageUrl = \Illuminate\Support\Str::startsWith($mediaUrl, ['http://', 'https://', '/'])
                                ? $mediaUrl
                                : asset('storage/'.$mediaUrl);
                        @endphp

                        <figure class="detail-gallery__item {{ $loop->first ? 'detail-gallery__item--featured' : '' }}">
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $media->descricao_acessibilidade ?: $media->titulo ?: $name }}"
                            >

                            @if ($media->titulo || $media->autor)
                                <figcaption>
                                    @if ($media->titulo)
                                        <strong>{{ $media->titulo }}</strong>
                                    @endif

                                    @if ($media->autor)
                                        <span>Foto: {{ $media->autor }}</span>
                                    @endif
                                </figcaption>
                            @endif
                        </figure>
                    @endforeach
                </div>
            @elseif ($singleImageUrl)
                <figure class="detail-gallery detail-gallery--fallback" aria-label="Foto de {{ $name }}">
                    <div class="detail-gallery__item detail-gallery__item--featured">
                        <img src="{{ $singleImageUrl }}" alt="{{ $name }}">
                        <figcaption>
                            <strong>{{ $name }}</strong>
                            <span>Informação oficial validada pelo município</span>
                        </figcaption>
                    </div>
                </figure>
            @else
                <figure class="detail-gallery detail-gallery--fallback" aria-label="Imagem ilustrativa de {{ $name }}">
                    <div class="detail-gallery__item detail-gallery__item--featured">
                        <img src="{{ $fallbackImage }}" alt="{{ $name }}">
                        <figcaption>
                            <strong>{{ $name }}</strong>
                            <span>Informação oficial validada pelo município</span>
                        </figcaption>
                    </div>
                </figure>
            @endif

            <article class="detail-copy">
                <p class="eyebrow">Sobre este atrativo</p>
                <h2>Informações Oficiais</h2>
                
                <p style="font-size: 1.1rem; line-height: 1.7; color: #334155; margin-bottom: 24px;">
                    {{ $desc }}
                </p>

                {{-- Informações de Destaque / Metadados --}}
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
                    <h3 style="font-size: 1rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 16px;">Detalhes da Visita</h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                        <div>
                            <strong style="display: block; font-size: 0.85rem; color: #64748b;">Localização</strong>
                            <span style="font-size: 0.95rem; color: #0f172a;">📍 {{ $location }}</span>
                        </div>

                        @if (isset($item->duration_minutes) || isset($item->total_duration_minutes))
                            <div>
                                <strong style="display: block; font-size: 0.85rem; color: #64748b;">Duração sugerida</strong>
                                <span style="font-size: 0.95rem; color: #0f172a;">⏱ {{ $item->duration_minutes ?? $item->total_duration_minutes }} minutos</span>
                            </div>
                        @endif

                        @if (isset($item->average_cost))
                            <div>
                                <strong style="display: block; font-size: 0.85rem; color: #64748b;">Custo médio</strong>
                                <span style="font-size: 0.95rem; color: #0f172a;">💵 {{ $item->average_cost > 0 ? 'R$ '.number_format($item->average_cost, 2, ',', '.') : 'Gratuito' }}</span>
                            </div>
                        @elseif (isset($item->price_range))
                            <div>
                                <strong style="display: block; font-size: 0.85rem; color: #64748b;">Faixa de Preço</strong>
                                <span style="font-size: 0.95rem; color: #0f172a;">💵 {{ $item->price_range }}</span>
                            </div>
                        @endif

                        @if (isset($item->is_outdoor))
                            <div>
                                <strong style="display: block; font-size: 0.85rem; color: #64748b;">Ambiente</strong>
                                <span style="font-size: 0.95rem; color: #0f172a;">{{ $item->is_outdoor ? '☀️ Ao ar livre' : '🏠 Espaço Coberto / Protegido' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Recursos de Acessibilidade --}}
                @if (!empty($item->accessibilityFeatures) && count($item->accessibilityFeatures) > 0)
                    <div style="margin-bottom: 24px;">
                        <p class="eyebrow" style="margin-bottom: 8px;">Acessibilidade e Inclusão</p>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            @foreach ($item->accessibilityFeatures as $feature)
                                <span style="display: inline-flex; align-items: center; gap: 4px; background: #e0f2fe; color: #0369a1; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 500;">
                                    ✓ {{ $feature->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Tags / Palavras-chave --}}
                @if (!empty($item->tags) && is_array($item->tags))
                    <div style="margin-bottom: 24px;">
                        <p class="eyebrow" style="margin-bottom: 8px;">Destaques e Tags</p>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            @foreach ($item->tags as $tag)
                                <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem;">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Contatos e Ações --}}
                @if (!empty($item->whatsapp) || !empty($item->phone) || !empty($item->instagram))
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 24px; margin-bottom: 32px;">
                        @if (!empty($item->whatsapp))
                            <a class="route-cta" href="https://wa.me/55{{ preg_replace('/\D/', '', $item->whatsapp) }}" target="_blank" rel="noopener noreferrer" style="text-decoration: none;">
                                Falar no WhatsApp
                            </a>
                        @endif

                        @if (!empty($item->phone))
                            <a class="text-link" href="tel:{{ preg_replace('/\D/', '', $item->phone) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; color: #0f172a;">
                                📞 {{ $item->phone }}
                            </a>
                        @endif
                    </div>
                @endif

                <a class="text-link" href="{{ route($routePrefix.'.index') }}">
                    ← Voltar para {{ str($catalogTitle)->lower() }}
                </a>
            </article>
        </div>
    </section>
@endsection
