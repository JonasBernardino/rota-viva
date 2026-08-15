@extends('layouts.public')

@section('title', 'Criar município')
@section('description', 'Cadastro de nova cidade na plataforma Rota Viva.')

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">Superadmin</p>
            <h1>Criar nova cidade</h1>
            <p>O Rota Viva criará o município, domínio, partição lógica por município e o primeiro gestor municipal.</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container admin-form-shell">
            @if ($errors->any())
                <div class="form-alert form-alert--danger" role="alert">
                    <strong>Revise os dados:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="admin-form" action="{{ route('platform.municipalities.store') }}" method="post">
                @csrf

                <div>
                    <p class="eyebrow">Dados da cidade</p>
                    <h2>Identificação municipal</h2>
                </div>

                <div class="admin-form__grid">
                    <label>
                        Nome do município
                        <input name="nome" value="{{ old('nome') }}" required placeholder="Ex.: Cabedelo">
                    </label>

                    <label>
                        Slug
                        <input name="slug" value="{{ old('slug') }}" placeholder="cabedelo">
                    </label>

                    <label>
                        Código IBGE
                        <input name="codigo_ibge" value="{{ old('codigo_ibge') }}" placeholder="2503201">
                    </label>

                    <label>
                        UF
                        <input name="uf" value="{{ old('uf') }}" required maxlength="2" placeholder="PB">
                    </label>

                    <label class="admin-form__full">
                        Domínio ou subdomínio
                        <input name="dominio" value="{{ old('dominio') }}" required placeholder="rota-viva.cabedelo.test">
                    </label>
                </div>

                <div>
                    <p class="eyebrow">Primeiro acesso</p>
                    <h2>Gestor municipal</h2>
                </div>

                <div class="admin-form__grid">
                    <label>
                        Nome do gestor
                        <input name="gestor_nome" value="{{ old('gestor_nome') }}" required placeholder="Secretaria de Turismo">
                    </label>

                    <label>
                        E-mail do gestor
                        <input type="email" name="gestor_email" value="{{ old('gestor_email') }}" required placeholder="gestor@cidade.pb.gov.br">
                    </label>

                    <label>
                        Senha inicial
                        <input type="password" name="gestor_senha" required minlength="8" placeholder="Mínimo 8 caracteres">
                    </label>
                </div>

                <div class="admin-form__actions">
                    <a href="{{ route('platform.dashboard') }}">Cancelar</a>
                    <button class="route-cta" type="submit">Criar cidade</button>
                </div>
            </form>
        </div>
    </section>
@endsection
