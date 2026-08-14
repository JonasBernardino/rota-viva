<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Descubra experiências e crie rotas personalizadas para viver a cidade no seu ritmo.">

        <title>Rota Viva — Turismo inteligente</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <a class="skip-link" href="#conteudo">Ir para o conteúdo principal</a>

        <div class="accessibility-bar" aria-label="Ferramentas de acessibilidade">
            <div class="container-fluid page-container accessibility-bar__content">
                <div class="accessibility-tools">
                    <button class="utility-button utility-button--accessibility" type="button" aria-label="Abrir opções de acessibilidade">
                        <span class="accessibility-symbol" aria-hidden="true">✦</span>
                        <span>Acessibilidade</span>
                    </button>
                    <span class="utility-divider" aria-hidden="true"></span>
                    <button class="utility-button" id="decrease-font" type="button" aria-label="Diminuir tamanho do texto">A−</button>
                    <button class="utility-button" id="increase-font" type="button" aria-label="Aumentar tamanho do texto">A+</button>
                    <button class="utility-button" id="contrast-toggle" type="button" aria-pressed="false">
                        <span class="contrast-symbol" aria-hidden="true"></span>
                        <span>Alto contraste</span>
                    </button>
                </div>

                <div class="locale-tools">
                    <button class="utility-button" type="button" aria-label="Selecionar idioma">
                        <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"></path></svg>
                        <span>PT</span>
                        <span aria-hidden="true">⌄</span>
                    </button>
                    <span class="utility-divider" aria-hidden="true"></span>
                    <button class="utility-button" type="button" aria-label="Selecionar município">
                        <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>
                        <span>Selecione o município</span>
                        <span aria-hidden="true">⌄</span>
                    </button>
                </div>
            </div>
        </div>

        <header class="site-header">
            <nav class="navbar navbar-expand-lg page-container" aria-label="Navegação principal">
                <a class="brand" href="{{ route('home') }}" aria-label="Rota Viva — página inicial">ROTA VIVA</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navigation" aria-controls="main-navigation" aria-expanded="false" aria-label="Abrir navegação">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="main-navigation">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="#descubra">Descubra</a></li>
                        <li class="nav-item"><a class="nav-link" href="#experiencias">Experiências</a></li>
                        <li class="nav-item"><a class="nav-link" href="#agenda">Agenda</a></li>
                        <li class="nav-item"><a class="nav-link" href="#onde-ficar">Onde ficar</a></li>
                        <li class="nav-item"><a class="nav-link" href="#onde-comer">Onde comer</a></li>
                    </ul>

                    <a class="manager-link" href="#gestor">
                        <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 21v-2a7 7 0 0 1 14 0v2Z"></path></svg>
                        <span>Área do gestor</span>
                    </a>
                </div>
            </nav>
        </header>

        <main id="conteudo">
            <section class="hero" aria-labelledby="hero-title">
                <div class="hero__copy">
                    <div class="hero__copy-inner">
                        <p class="eyebrow">Turismo inteligente</p>
                        <h1 id="hero-title">Como você quer<br>viver a cidade hoje?</h1>
                        <p class="hero__description">Conte o que você procura e receba uma experiência que se adapta ao seu tempo, orçamento e interesses.</p>

                        <form class="route-search" action="#descubra" method="get">
                            <label class="visually-hidden" for="route-query">Descreva a experiência que deseja</label>
                            <div class="route-search__field">
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.5 9.7 9.7 0 0 1-4-.8L3 21l1.5-4.2A8.5 8.5 0 1 1 21 11.5Z"></path></svg>
                                <input id="route-query" name="q" type="text" placeholder="Ex.: Quero cultura e tranquilidade, tenho 4 horas...">
                            </div>
                            <button class="route-search__button" type="submit">
                                <span>Criar minha rota</span>
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M14 6l6 6-6 6"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="hero__visual">
                    <img src="{{ asset('images/rota-viva-hero.webp') }}" alt="Cidade histórica à beira-mar, cercada por montanhas e natureza">
                    <div class="experience-card">
                        <div class="experience-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48"><path d="M30 10c0 7-8 13-8 13s-8-6-8-13a8 8 0 1 1 16 0Z"></path><circle cx="22" cy="10" r="2.5"></circle><path class="dashed" d="M11 34c3-6 8-2 11-6s10-3 14 2-2 9-8 7-7 3-10 5"></path></svg>
                        </div>
                        <div>
                            <strong>Sua experiência, em movimento</strong>
                            <div class="experience-card__tags">
                                <span>◷&nbsp; 4 horas</span>
                                <span>♧&nbsp; Família</span>
                                <span>▥&nbsp; Cultura</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="trust-strip" aria-label="Diferenciais do Rota Viva">
                <div class="page-container trust-strip__grid">
                    <div class="trust-item">
                        <span class="trust-item__icon trust-item__icon--filled" aria-hidden="true">✓</span>
                        <span>Informações validadas<br>pelo município</span>
                    </div>
                    <div class="trust-item">
                        <svg class="trust-item__svg" aria-hidden="true" viewBox="0 0 32 32"><path d="M3 12 16 4l13 8M6 13h20M8 14v11M13 14v11M19 14v11M24 14v11M4 27h24"></path></svg>
                        <span>Atrativos oficiais</span>
                    </div>
                    <div class="trust-item">
                        <svg class="trust-item__svg" aria-hidden="true" viewBox="0 0 32 32"><path d="M6 6h11c8 0 9 8 3 10l-9 2c-6 2-5 8 2 8h13"></path><circle cx="5" cy="6" r="2"></circle><circle cx="27" cy="26" r="2"></circle></svg>
                        <span>Rotas adaptativas</span>
                    </div>
                    <div class="trust-item">
                        <svg class="trust-item__svg" aria-hidden="true" viewBox="0 0 32 32"><circle cx="16" cy="16" r="13"></circle><circle cx="16" cy="8" r="2"></circle><path d="M8 12h16M16 12v13M11 16l5-4 5 4M12 26l4-7 4 7"></path></svg>
                        <span>Acessibilidade detalhada</span>
                    </div>
                </div>
            </section>

            <section class="discover-section page-container" id="descubra" aria-labelledby="discover-title">
                <div class="section-heading">
                    <h2 id="discover-title">Descubra no seu ritmo</h2>
                    <span aria-hidden="true"></span>
                </div>

                <div class="discovery-grid" id="experiencias">
                    <a class="discovery-item" href="#cultura">
                        <span class="discovery-item__number">01</span>
                        <span class="discovery-item__label">Cultura e história</span>
                        <span class="discovery-item__arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="discovery-item" href="#natureza">
                        <span class="discovery-item__number">02</span>
                        <span class="discovery-item__label">Natureza</span>
                        <span class="discovery-item__arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="discovery-item" href="#gastronomia">
                        <span class="discovery-item__number">03</span>
                        <span class="discovery-item__label">Gastronomia</span>
                        <span class="discovery-item__arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="discovery-item" href="#familias">
                        <span class="discovery-item__number">04</span>
                        <span class="discovery-item__label">Para famílias</span>
                        <span class="discovery-item__arrow" aria-hidden="true">→</span>
                    </a>
                </div>
            </section>
        </main>
    </body>
</html>
