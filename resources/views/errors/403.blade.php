@extends('layouts.public')

@section('title', 'Acesso não autorizado')
@section('description', 'Você não tem permissão para acessar esta área do Rota Viva.')

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">Acesso protegido</p>
            <h1>Você não tem permissão para acessar esta área</h1>
            <p>
                O painel municipal é reservado para gestores autorizados. Você ainda pode continuar explorando
                a cidade e criar sua própria rota personalizada.
            </p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container editorial-content">
            <p>
                Se você faz parte da gestão municipal, confira se entrou com o e-mail correto ou solicite a liberação
                do seu acesso ao administrador da plataforma.
            </p>

            <div class="admin-form__actions">
                <a href="{{ route('routes.create') }}">Criar minha rota</a>
                <a class="route-cta" href="{{ route('home') }}">Voltar para a página inicial</a>
            </div>
        </div>
    </section>
@endsection
