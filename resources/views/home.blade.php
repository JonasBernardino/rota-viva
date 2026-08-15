<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Descubra experiências e crie rotas personalizadas para viver a cidade no seu ritmo.">

        <title>{{ $homeContent['brand_name'] ?? 'Rota Viva' }} — Turismo inteligente</title>

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
                    <button class="utility-button" id="theme-toggle" type="button" aria-pressed="false">
                        <span class="theme-symbol" aria-hidden="true">☾</span>
                        <span class="theme-label">Tema escuro</span>
                    </button>
                </div>

                <div class="locale-tools">
                    <button class="utility-button" type="button" aria-label="Selecionar idioma">
                        <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"></path></svg>
                        <span>PT</span>
                        <span aria-hidden="true">⌄</span>
                    </button>
                    <span class="utility-divider" aria-hidden="true"></span>
                    @include('partials.municipality-selector')
                </div>
            </div>
        </div>

        <header class="site-header">
            <nav class="navbar navbar-expand-lg page-container" aria-label="Navegação principal">
                @include('partials.brand')

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navigation" aria-controls="main-navigation" aria-expanded="false" aria-label="Abrir navegação">
                    <span class="navbar-toggler-icon"></span>
                </button>

                @include('partials.public-navigation')
            </nav>
        </header>

        <main id="conteudo">
            <section class="hero" aria-labelledby="hero-title">
                <div class="hero__copy">
                    <div class="hero__copy-inner">
                        <p class="eyebrow">{{ $homeContent['hero_eyebrow'] }}</p>
                        <h1 id="hero-title">{!! nl2br(e($homeContent['hero_title'])) !!}</h1>
                        <p class="hero__description">{{ $homeContent['hero_description'] }}</p>

                        <form class="route-search" action="{{ route('routes.create') }}" method="get">
                            <label class="visually-hidden" for="route-query">Descreva a experiência que deseja</label>
                            <div class="route-search__field">
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.5 9.7 9.7 0 0 1-4-.8L3 21l1.5-4.2A8.5 8.5 0 1 1 21 11.5Z"></path></svg>
                                <input id="route-query" name="q" type="text" placeholder="{{ $homeContent['hero_search_placeholder'] }}">
                            </div>
                            <button class="route-search__button" type="submit">
                                <span>Criar minha rota</span>
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M14 6l6 6-6 6"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="hero__visual">
                    <img src="{{ $homeContent['hero_image_url'] }}" alt="{{ $homeContent['hero_image_alt'] }}">
                    <div class="experience-card">
                        <div class="experience-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48"><path d="M30 10c0 7-8 13-8 13s-8-6-8-13a8 8 0 1 1 16 0Z"></path><circle cx="22" cy="10" r="2.5"></circle><path class="dashed" d="M11 34c3-6 8-2 11-6s10-3 14 2-2 9-8 7-7 3-10 5"></path></svg>
                        </div>
                        <div>
                            <strong>{{ $homeContent['hero_card_title'] }}</strong>
                            <div class="experience-card__tags">
                                @foreach ($homeContent['hero_card_tags'] as $tag)
                                    <span>{{ $loop->first ? '◷' : ($loop->iteration === 2 ? '♧' : '▥') }}&nbsp; {{ $tag }}</span>
                                @endforeach
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
                    <a class="discovery-item" href="{{ route('culture.index') }}">
                        <span class="discovery-item__number">01</span>
                        <span class="discovery-item__label">Cultura e história</span>
                        <span class="discovery-item__arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="discovery-item" href="{{ route('tourist-spots.index') }}">
                        <span class="discovery-item__number">02</span>
                        <span class="discovery-item__label">Natureza</span>
                        <span class="discovery-item__arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="discovery-item" href="{{ route('dining.index') }}">
                        <span class="discovery-item__number">03</span>
                        <span class="discovery-item__label">Gastronomia</span>
                        <span class="discovery-item__arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="discovery-item" href="{{ route('tours.index') }}">
                        <span class="discovery-item__number">04</span>
                        <span class="discovery-item__label">Para famílias</span>
                        <span class="discovery-item__arrow" aria-hidden="true">→</span>
                    </a>
                </div>
            </section>

            <section class="territory-highlights page-container" id="atrativos" aria-labelledby="highlights-title">
                <div class="highlights-heading">
                    <div>
                        <p class="eyebrow">Destaques do território</p>
                        <h2 id="highlights-title">Lugares que<br>revelam a cidade</h2>
                    </div>
                    <a class="text-link" href="{{ route('tourist-spots.index') }}">Ver todos os atrativos <span aria-hidden="true">→</span></a>
                </div>

                <div class="places-grid">
                    @forelse ($featuredPlaces as $place)
                        @php
                            $fallbackImages = [
                                asset('images/cultural-center.webp'),
                                asset('images/local-market.webp'),
                                asset('images/rota-viva-hero.webp'),
                            ];
                            $media = $place->midias->firstWhere('is_destaque', true) ?? $place->midias->first();
                            $mediaUrl = $media?->url;
                            $imageUrl = $mediaUrl
                                ? (\Illuminate\Support\Str::startsWith($mediaUrl, ['http://', 'https://', '/']) ? $mediaUrl : asset('storage/'.$mediaUrl))
                                : $fallbackImages[$loop->index] ?? asset('images/rota-viva-hero.webp');
                            $categoryName = $place->categoria?->nome ?? 'Atrativo oficial';
                        @endphp

                        <article class="place-card {{ ['place-card--culture', 'place-card--market', 'place-card--viewpoint'][$loop->index] ?? '' }}" id="{{ $place->slug }}">
                            <img src="{{ $imageUrl }}" alt="{{ $media?->descricao_acessibilidade ?: $place->nome }}">
                            <a class="place-card__content" href="{{ route('tourist-spots.show', $place->slug) }}">
                                <span>
                                    <strong>{{ $place->nome }}</strong>
                                    <small>{{ $categoryName }}&nbsp;&nbsp;·&nbsp;&nbsp;{{ $place->duracao_minutos }} min</small>
                                </span>
                                <span class="place-card__arrow" aria-hidden="true">→</span>
                            </a>
                        </article>
                    @empty
                        <article class="place-card place-card--culture" id="cultura">
                            <img src="{{ asset('images/cultural-center.webp') }}" alt="Centro cultural instalado em construção histórica com portas verdes">
                            <a class="place-card__content" href="{{ route('tourist-spots.index') }}">
                                <span>
                                    <strong>Atrativos oficiais da cidade</strong>
                                    <small>Conteúdo municipal&nbsp;&nbsp;·&nbsp;&nbsp;em cadastro</small>
                                </span>
                                <span class="place-card__arrow" aria-hidden="true">→</span>
                            </a>
                        </article>
                    @endforelse
                </div>

                <p class="municipal-validation">
                    <span aria-hidden="true">✓</span>
                    Informação validada pelo município
                </p>
            </section>

            <section class="adaptive-route" id="rota-adaptativa" aria-labelledby="adaptive-title">
                <div class="page-container adaptive-route__grid">
                    <div class="adaptive-route__copy">
                        <p class="eyebrow">Rota adaptativa</p>
                        <h2 id="adaptive-title">Uma rota que<br>acompanha a vida real</h2>
                        <p>Tempo, orçamento, companhia e acessibilidade orientam cada escolha. Se algo mudar, sua experiência também muda.</p>

                        <ol class="adaptive-steps">
                            <li><span>01</span><strong>Conte como quer viver a cidade</strong></li>
                            <li><span>02</span><strong>Receba uma experiência pensada para você</strong></li>
                            <li><span>03</span><strong>Se algo mudar, sua rota também muda</strong></li>
                        </ol>
                    </div>

                    <div class="route-map" aria-label="Exemplo visual de uma rota adaptada após o início da chuva">
                        <img src="{{ $homeContent['hero_image_url'] }}" alt="">
                        <div class="route-map__shade"></div>
                        <svg class="route-map__path" aria-hidden="true" viewBox="0 0 620 440" preserveAspectRatio="none">
                            <path class="route-map__old" d="M145 48 C120 95 145 134 220 160 C280 182 315 235 265 276 C235 302 260 355 340 390"></path>
                            <path class="route-map__new" d="M145 48 C120 95 145 134 220 160 C250 177 246 222 226 255 C205 290 255 335 340 390"></path>
                            <circle cx="145" cy="48" r="7"></circle>
                            <circle cx="220" cy="160" r="7"></circle>
                            <circle cx="340" cy="390" r="7"></circle>
                        </svg>

                        <span class="route-point route-point--one">Centro de Cultura<br>e Memória · 1h</span>
                        <span class="route-point route-point--two">Mercado de<br>Sabores Locais · 1h30</span>
                        <span class="route-point route-point--three">Praça da Matriz<br>· 1h30</span>

                        <div class="route-summary">
                            <span>◷&nbsp;&nbsp;4 horas</span>
                            <span>R$&nbsp;&nbsp;135 estimados</span>
                        </div>

                        <div class="rain-card">
                            <strong>☂&nbsp;&nbsp;Começou a chover</strong>
                            <p>Mirante substituído por experiência cultural coberta</p>
                            <div><span class="rain-card__old"></span>Rota anterior</div>
                            <div><span class="rain-card__new"></span>Nova rota</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="confidence-section" id="confianca" aria-labelledby="confidence-title">
                <div class="page-container">
                    <div class="confidence-heading">
                        <div>
                            <h2 id="confidence-title">Informação para visitar com confiança</h2>
                            <p>Conteúdo organizado e mantido em parceria com a gestão municipal.</p>
                        </div>
                        <div class="municipal-mark" aria-label="Gestão municipal">
                            <span aria-hidden="true">◇</span>
                            <small>Gestão<br>Municipal</small>
                        </div>
                    </div>

                    <div class="confidence-grid">
                        <article>
                            <svg aria-hidden="true" viewBox="0 0 40 40"><circle cx="20" cy="20" r="17"></circle><path d="M20 10v11l8 5"></path></svg>
                            <h3>Horários<br>atualizados</h3>
                            <p>Informações sempre conferidas para você planejar sem imprevistos.</p>
                        </article>
                        <article>
                            <svg aria-hidden="true" viewBox="0 0 40 40"><circle cx="20" cy="20" r="17"></circle><circle cx="20" cy="11" r="2"></circle><path d="M10 16h20M20 16v15M14 31l6-10 6 10"></path></svg>
                            <h3>Acessibilidade<br>detalhada</h3>
                            <p>Caminhos, recursos e apoios descritos com clareza e responsabilidade.</p>
                        </article>
                        <article>
                            <svg aria-hidden="true" viewBox="0 0 40 40"><circle cx="20" cy="20" r="17"></circle><path d="M20 10v20M25 14c-2-3-10-3-10 2 0 6 11 2 11 8 0 5-8 6-12 2"></path></svg>
                            <h3>Custos e<br>duração</h3>
                            <p>Estimativas de tempo e custo para você fazer escolhas conscientes.</p>
                        </article>
                        <article>
                            <svg aria-hidden="true" viewBox="0 0 40 40"><circle cx="20" cy="20" r="17"></circle><path d="M26 16c0 6-6 12-6 12s-6-6-6-12a6 6 0 1 1 12 0Z"></path><circle cx="20" cy="16" r="2"></circle></svg>
                            <h3>Orientações<br>locais</h3>
                            <p>Regras, cuidados e dicas do território reunidos em um só lugar.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="local-economy" id="economia-local" aria-labelledby="economy-title">
                <div class="local-economy__copy">
                    <div>
                        <p class="eyebrow">{{ $homeContent['local_economy_eyebrow'] }}</p>
                        <h2 id="economy-title">{!! nl2br(e($homeContent['local_economy_title'])) !!}</h2>
                        <p>{{ $homeContent['local_economy_description'] }}</p>
                        @if ($homeContent['local_economy_stat'])
                            <p class="local-opportunities"><span aria-hidden="true">♧</span> {{ $homeContent['local_economy_stat'] }}</p>
                        @endif
                        @if ($homeContent['local_economy_link_label'])
                            <a class="text-link" href="{{ $homeContent['local_economy_link_url'] }}">{{ $homeContent['local_economy_link_label'] }} <span aria-hidden="true">→</span></a>
                        @endif
                    </div>
                </div>
                <div class="local-economy__image">
                    <img class="local-economy__image-bg" src="{{ $homeContent['local_economy_image_url'] }}" alt="" aria-hidden="true">
                    <img class="local-economy__image-main" src="{{ $homeContent['local_economy_image_url'] }}" alt="{{ $homeContent['local_economy_image_alt'] }}">
                </div>
            </section>

            <section class="final-cta" id="comece-agora" aria-labelledby="final-cta-title">
                <img src="{{ $homeContent['hero_image_url'] }}" alt="{{ $homeContent['hero_image_alt'] }}">
                <div class="page-container final-cta__content">
                    <div class="final-cta__panel">
                        <h2 id="final-cta-title">Sua próxima<br>experiência começa aqui</h2>
                        <p>Diga quanto tempo você tem e como deseja viver a cidade. O Rota Viva cuida do percurso.</p>
                        <a class="final-cta__button" href="{{ route('routes.create') }}">Criar minha rota <span aria-hidden="true">→</span></a>
                        <a class="final-cta__secondary" href="{{ route('tourist-spots.index') }}">Explorar sem roteiro</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer" id="contato">
            <div class="page-container">
                <div class="site-footer__grid">
                    <div class="site-footer__brand">
                        <a class="brand brand--light" href="{{ route('home') }}">ROTA VIVA</a>
                        <p>Turismo inteligente para<br>territórios vivos.</p>
                        <div class="social-links" aria-label="Redes sociais">
                            <a href="#instagram" aria-label="Instagram">◎</a>
                            <a href="#facebook" aria-label="Facebook">f</a>
                            <a href="#youtube" aria-label="YouTube">▶</a>
                            <a href="#email" aria-label="E-mail">✉</a>
                        </div>
                    </div>

                    <div>
                        <h2>Descubra</h2>
                        <a href="{{ route('tourist-spots.index') }}">Pontos turísticos</a>
                        <a href="{{ route('experiences') }}">Experiências</a>
                        <a href="{{ route('agenda.index') }}">Agenda</a>
                    </div>
                    <div>
                        <h2>Planeje</h2>
                        <a href="{{ route('routes.create') }}">Criar rota</a>
                        <a href="{{ route('stays.index') }}">Onde ficar</a>
                        <a href="{{ route('dining.index') }}">Onde comer</a>
                    </div>
                    <div>
                        <h2>Institucional</h2>
                        <a href="{{ route('about') }}">Sobre o projeto</a>
                        @auth
                            @can('access-admin-panel')
                                <a href="{{ route('admin.dashboard') }}">Área do gestor</a>
                            @endcan
                        @endauth
                        <a href="{{ route('contact') }}">Contato</a>
                    </div>
                    <div>
                        <h2>Acessibilidade</h2>
                        <a href="{{ route('accessibility.resources') }}">Recursos</a>
                        <a href="{{ route('accessibility.statement') }}">Declaração</a>
                        <a href="{{ route('help') }}">Ajuda</a>
                    </div>
                </div>

                <div class="site-footer__bottom">
                    <span>♢&nbsp;&nbsp;Informações oficiais do município</span>
                    <span><a href="{{ route('privacy') }}">Privacidade</a>&nbsp;&nbsp;·&nbsp;&nbsp;<a href="{{ route('terms') }}">Termos de uso</a></span>
                    <span>◉&nbsp;&nbsp;PT⌄</span>
                </div>
            </div>
        </footer>
    </body>
</html>
