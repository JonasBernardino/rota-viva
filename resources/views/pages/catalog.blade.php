@extends('layouts.public')
@section('title', $title)
@section('description', $description)

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">{{ $eyebrow }}</p>
            <h1>{{ $title }}</h1>
            <p>{{ $description }}</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container">
            <div class="catalog-toolbar" aria-label="Informações do catálogo">
                <strong>Catálogo Oficial de {{ $currentTenant->name ?? 'Lucena' }}</strong>
                <span>{{ count($items) }} {{ count($items) === 1 ? 'item oficial disponível' : 'itens oficiais disponíveis' }}</span>
            </div>

            <div class="catalog-grid">
                @forelse ($items as $item)
                    @php
                        $name = $item->name ?? $item->title ?? 'Item';
                        $slug = $item->slug ?? str($name)->slug();
                        $desc = $item->description ?? $item->summary ?? '';
                        $categoryName = $item->category->name ?? (is_string($item->category ?? null) ? ucfirst($item->category) : null);
                        $location = $item->neighborhood ?? $item->location_name ?? $item->address ?? null;
                    @endphp

                    <article class="catalog-card">
                        <div class="catalog-card__placeholder" aria-hidden="true">
                            <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="catalog-card__content">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                <p class="eyebrow mb-0">
                                    @if ($categoryName)
                                        {{ $categoryName }}
                                    @elseif (!empty($item->has_seal_of_quality))
                                        ★ Selo Validado
                                    @elseif (isset($item->starts_at))
                                        {{ \Carbon\Carbon::parse($item->starts_at)->translatedFormat('d \d\e M') }}
                                    @else
                                        Oficial
                                    @endif
                                </p>

                                @if (!empty($item->has_seal_of_quality))
                                    <span class="badge" style="background-color: var(--color-gold-tint, #fef3c7); color: #92400e; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; font-weight: 600;">
                                        Selo de Qualidade
                                    </span>
                                @endif
                            </div>

                            <h2>{{ $name }}</h2>
                            <p>{{ Str::limit($desc, 130) }}</p>

                            <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 12px; display: flex; flex-wrap: wrap; gap: 8px;">
                                @if ($location)
                                    <span>📍 {{ $location }}</span>
                                @endif

                                @if (isset($item->duration_minutes) || isset($item->total_duration_minutes))
                                    <span>⏱ {{ $item->duration_minutes ?? $item->total_duration_minutes }} min</span>
                                @endif

                                @if (isset($item->average_cost))
                                    <span>💵 {{ $item->average_cost > 0 ? 'R$ '.number_format($item->average_cost, 2, ',', '.') : 'Gratuito' }}</span>
                                @elseif (isset($item->price_range))
                                    <span>💵 {{ $item->price_range }}</span>
                                @endif
                            </div>

                            <a class="text-link" href="{{ route($routePrefix.'.show', $slug) }}">
                                Ver detalhes completos <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>
                @empty
                    <div style="grid-column: 1 / -1; padding: 40px 20px; text-align: center; background: #f8fafc; border-radius: 8px;">
                        <p class="eyebrow">Nenhum item encontrado</p>
                        <h3>Novos cadastros oficiais em breve</h3>
                        <p>O município está atualizando a base oficial de atrativos e estabelecimentos.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
