@extends('layouts.public')

@section('title', 'Painel da Plataforma')
@section('description', 'Administração geral dos municípios atendidos pelo Rota Viva.')

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">Superadmin</p>
            <h1>Painel da Plataforma</h1>
            <p>Crie cidades, acompanhe domínios e prepare novos ambientes municipais.</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container">
            @if (session('status'))
                <div class="admin-flash" role="status">{{ session('status') }}</div>
            @endif

            <div class="admin-placeholder">
                <div>
                    <p class="eyebrow">Municípios</p>
                    <h2>Cidades cadastradas</h2>
                    <p>{{ $municipalities->count() }} município(s) disponível(is) na plataforma.</p>
                </div>

                <a class="route-cta" href="{{ route('platform.municipalities.create') }}">Criar nova cidade</a>
            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Município</th>
                            <th>UF</th>
                            <th>Partição lógica</th>
                            <th>Status</th>
                            <th>Domínio principal</th>
                            <th>Domínios</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($municipalities as $municipality)
                            <tr>
                                <td>
                                    <strong>{{ $municipality->nome }}</strong>
                                    <br>
                                    <small>{{ $municipality->slug }}</small>
                                </td>
                                <td>{{ $municipality->uf }}</td>
                                <td><code>municipio_id={{ $municipality->id }}</code></td>
                                <td>{{ $municipality->status }}</td>
                                <td>{{ $municipality->dominios->firstWhere('is_principal', true)?->dominio ?? $municipality->dominios->first()?->dominio ?? '—' }}</td>
                                <td>{{ $municipality->dominios_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Nenhum município cadastrado ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
