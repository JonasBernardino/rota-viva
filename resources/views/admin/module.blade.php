@extends('layouts.public')
@section('title', $title . ' — Gestão Municipal')

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">← Painel Municipal</a>
            </p>
            <h1>{{ $title }}</h1>
            <p>{{ $description }}</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                <div>
                    <strong>{{ count($items) }}</strong> registros cadastrados no município
                </div>
                <div>
                    <button class="route-cta" type="button" style="padding: 8px 16px; font-size: 0.9rem;">
                        + Novo Cadastro
                    </button>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow-x: auto; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 0.85rem; text-transform: uppercase;">
                            <th style="padding: 12px 16px;">Nome / Identificação</th>
                            <th style="padding: 12px 16px;">Categoria / Tipo</th>
                            <th style="padding: 12px 16px;">Localização</th>
                            <th style="padding: 12px 16px;">Status</th>
                            <th style="padding: 12px 16px; text-align: right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            @php
                                $name = $item->name ?? $item->title ?? 'Item';
                                $categoryName = $item->category->name ?? (is_string($item->category ?? null) ? ucfirst($item->category) : ($item->business_type ?? 'Oficial'));
                                $location = $item->neighborhood ?? $item->location_name ?? $item->address ?? 'Lucena — PB';
                                $hasSeal = !empty($item->has_seal_of_quality);
                            @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 14px 16px; font-weight: 600; color: #0f172a;">
                                    {{ $name }}
                                </td>
                                <td style="padding: 14px 16px; color: #64748b;">
                                    {{ $categoryName }}
                                </td>
                                <td style="padding: 14px 16px; color: #64748b;">
                                    📍 {{ $location }}
                                </td>
                                <td style="padding: 14px 16px;">
                                    @if ($hasSeal)
                                        <span style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                            ★ Selo Validado
                                        </span>
                                    @else
                                        <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                            Ativo
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 14px 16px; text-align: right;">
                                    @if (isset($item->slug))
                                        <a href="{{ route($module === 'culture' ? 'culture.show' : ($module === 'tourist-spots' ? 'tourist-spots.show' : ($module === 'events' ? 'agenda.show' : ($module === 'tours' ? 'tours.show' : ($module === 'guides' ? 'guides.show' : 'stays.show')))), $item->slug) }}" target="_blank" style="color: #0284c7; text-decoration: none; font-weight: 500; font-size: 0.85rem;">
                                            Ver Público ↗
                                        </a>
                                    @else
                                        <span style="color: #94a3b8; font-size: 0.85rem;">Validado</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 32px 16px; text-align: center; color: #64748b;">
                                    Nenhum registro cadastrado neste módulo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
