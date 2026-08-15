@extends('layouts.public')
@section('title', 'Entrar — Rota Viva')
@section('description', 'Acesso administrativo e credenciado à plataforma Rota Viva.')

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">Acesso Restrito</p>
            <h1>Área do Gestor</h1>
            <p>Gerencie o conteúdo oficial, trade turístico e indicadores municipais.</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container" style="max-width: 480px; margin: 0 auto;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div style="background-color: #fee2e2; border: 1px solid #f87171; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                            <strong>Atenção:</strong>
                            <ul style="margin: 4px 0 0 16px; padding: 0;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div style="margin-bottom: 20px;">
                        <label for="email" style="display: block; font-weight: 600; font-size: 0.9rem; color: #1e293b; margin-bottom: 6px;">
                            E-mail Institucional
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', 'gestor@lucena.pb.gov.br') }}"
                            required
                            autofocus
                            style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; color: #0f172a;"
                            placeholder="seuemail@municipio.gov.br"
                        >
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label for="password" style="display: block; font-weight: 600; font-size: 0.9rem; color: #1e293b; margin-bottom: 6px;">
                            Senha de Acesso
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            value="12345678"
                            required
                            style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; color: #0f172a;"
                            placeholder="••••••••"
                        >
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; font-size: 0.875rem;">
                        <label style="display: flex; align-items: center; gap: 8px; color: #475569; cursor: pointer;">
                            <input type="checkbox" name="remember" value="1" checked style="accent-color: #0284c7;">
                            Lembrar meu acesso
                        </label>
                    </div>

                    <button class="route-cta" type="submit" style="width: 100%; justify-content: center; padding: 12px 20px; font-size: 1rem;">
                        Entrar no Painel
                    </button>
                </form>

                <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9; text-align: center; font-size: 0.85rem; color: #64748b;">
                    <p style="margin: 0;">Credenciais de demonstração (Lucena - PB):</p>
                    <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #0f172a; font-size: 0.8rem;">gestor@lucena.pb.gov.br / 12345678</code>
                </div>
            </div>
        </div>
    </section>
@endsection
