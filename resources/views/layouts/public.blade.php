<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="@yield('description', 'Descubra a cidade e planeje experiências pelo Rota Viva.')">
        <title>@yield('title') — Rota Viva</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <a class="skip-link" href="#conteudo">Ir para o conteúdo principal</a>
        <div class="accessibility-bar" aria-label="Ferramentas de acessibilidade">
            <div class="container-fluid page-container accessibility-bar__content">
                <div class="accessibility-tools">
                    <button class="utility-button utility-button--accessibility" type="button" aria-label="Abrir opções de acessibilidade"><span class="accessibility-symbol" aria-hidden="true">✦</span><span>Acessibilidade</span></button>
                    <span class="utility-divider" aria-hidden="true"></span>
                    <button class="utility-button" id="decrease-font" type="button" aria-label="Diminuir tamanho do texto">A−</button>
                    <button class="utility-button" id="increase-font" type="button" aria-label="Aumentar tamanho do texto">A+</button>
                    <button class="utility-button" id="contrast-toggle" type="button" aria-pressed="false"><span class="contrast-symbol" aria-hidden="true"></span><span>Alto contraste</span></button>
                </div>
                <div class="locale-tools">
                    <span>PT</span>
                    <span class="utility-divider" aria-hidden="true"></span>
                    @include('partials.municipality-selector')
                </div>
            </div>
        </div>
        <header class="site-header site-header--inner">
            <nav class="navbar navbar-expand-lg page-container" aria-label="Navegação principal">
                <a class="brand" href="{{ route('home') }}" aria-label="Rota Viva — página inicial">ROTA VIVA</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navigation" aria-controls="main-navigation" aria-expanded="false" aria-label="Abrir navegação"><span class="navbar-toggler-icon"></span></button>
                @include('partials.public-navigation')
            </nav>
        </header>
        <main id="conteudo" class="inner-page">@yield('content')</main>
        <footer class="inner-footer">
            <div class="page-container inner-footer__content">
                <div><a class="brand brand--light" href="{{ route('home') }}">ROTA VIVA</a><p>Turismo inteligente para territórios vivos.</p></div>
                <nav aria-label="Navegação do rodapé">
                    <a href="{{ route('tourist-spots.index') }}">Pontos turísticos</a><a href="{{ route('tours.index') }}">Passeios</a><a href="{{ route('agenda.index') }}">Agenda</a><a href="{{ route('about') }}">Sobre o projeto</a><a href="{{ route('help') }}">Ajuda</a>
                </nav>
            </div>
            <div class="page-container inner-footer__bottom"><span>Informações oficiais do município</span><span><a href="{{ route('privacy') }}">Privacidade</a> · <a href="{{ route('terms') }}">Termos de uso</a></span></div>
        </footer>
    </body>
</html>
