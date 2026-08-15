@extends('layouts.public')
@section('title', 'Entrar — Rota Viva')
@section('description', 'Acesso administrativo e credenciado à plataforma Rota Viva.')

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">Gestão municipal</p>
            <h1>Acesso ao painel</h1>
            <p>Área reservada para equipes autorizadas a manter as informações oficiais do município.</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container login-shell">
            <div class="login-card">
                <div class="login-card__intro">
                    <p class="eyebrow">Identificação</p>
                    <h2>Entre com sua conta de gestão</h2>
                    <p>
                        Use o e-mail autorizado pela gestão municipal para acessar cadastros, conteúdos oficiais
                        e indicadores do Rota Viva.
                    </p>
                </div>

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="form-alert form-alert--danger" role="alert">
                            <strong>Atenção:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="form-alert form-alert--success" role="status">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="form-field">
                        <label for="email">
                            E-mail
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', 'gestor@lucena.pb.gov.br') }}"
                            required
                            autofocus
                            placeholder="seuemail@municipio.gov.br"
                        >
                    </div>

                    <div class="form-field">
                        <label for="password">
                            Senha de acesso
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            placeholder="••••••••"
                        >
                    </div>

                    <div class="login-options">
                        <label>
                            <input type="checkbox" name="remember" value="1">
                            Lembrar meu acesso
                        </label>
                    </div>

                    <button class="route-cta login-submit" type="submit">
                        Entrar
                    </button>
                </form>

                <div class="demo-credentials">
                    <p>Credenciais de demonstração — Lucena/PB</p>
                    <code>gestor@lucena.pb.gov.br / 12345678</code>
                </div>
            </div>
        </div>
    </section>
@endsection
