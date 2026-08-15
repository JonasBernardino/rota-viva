@extends('layouts.public')

@section('title', 'Aparência da cidade — Gestão Municipal')

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">← Painel Municipal</a>
            </p>
            <h1>Aparência da cidade</h1>
            <p>Personalize a marca, a logo, o banner inicial e os blocos principais exibidos no portal público deste município.</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container admin-form-shell">
            @if (session('status'))
                <div class="form-alert form-alert--success" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="route-builder-error" role="alert">
                    <span class="route-builder-error__icon" aria-hidden="true">!</span>
                    <div>
                        <strong>Revise os campos destacados.</strong>
                        <p>Algumas informações obrigatórias não foram preenchidas corretamente.</p>
                    </div>
                </div>
            @endif

            <form class="admin-form" action="{{ route('admin.appearance.update') }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="admin-form__grid">
                    <label>
                        Nome da marca
                        <input name="brand_name" value="{{ old('brand_name', $municipality->brand_name ?? 'ROTA VIVA') }}" required maxlength="80">
                    </label>

                    <label>
                        Logo da cidade ou projeto
                        <input name="brand_logo" type="file" accept="image/*">
                    </label>
                </div>

                @if ($municipality?->brandLogoUrl())
                    <div class="appearance-preview">
                        <span>Logo atual</span>
                        <img src="{{ $municipality->brandLogoUrl() }}" alt="">
                    </div>
                @endif

                <div class="admin-form__grid">
                    <label>
                        Chamada pequena do banner
                        <input name="hero_eyebrow" value="{{ old('hero_eyebrow', $municipality->hero_eyebrow ?? 'Turismo inteligente') }}" required maxlength="80">
                    </label>

                    <label>
                        Título do banner
                        <input name="hero_title" value="{{ old('hero_title', $municipality->hero_title ?? 'Como você quer viver a cidade hoje?') }}" required maxlength="120">
                    </label>
                </div>

                <label>
                    Descrição do banner
                    <textarea name="hero_description" rows="4" required maxlength="280">{{ old('hero_description', $municipality->hero_description ?? 'Conte o que você procura e receba uma experiência que se adapta ao seu tempo, orçamento e interesses.') }}</textarea>
                </label>

                <div class="admin-form__grid">
                    <label>
                        Imagem do banner inicial
                        <input name="hero_image" type="file" accept="image/*">
                    </label>

                    <label>
                        Texto alternativo da imagem
                        <input name="hero_image_alt" value="{{ old('hero_image_alt', $municipality->hero_image_alt ?? '') }}" maxlength="180" placeholder="Ex.: Vista da praia e do centro histórico">
                    </label>
                </div>

                @if ($municipality?->heroImageUrl())
                    <div class="appearance-preview appearance-preview--wide">
                        <span>Banner atual</span>
                        <img src="{{ $municipality->heroImageUrl() }}" alt="">
                    </div>
                @endif

                <div class="admin-form__grid">
                    <label>
                        Placeholder do campo “Criar minha rota”
                        <input name="hero_search_placeholder" value="{{ old('hero_search_placeholder', $municipality->hero_search_placeholder ?? 'Ex.: Quero cultura e tranquilidade, tenho 4 horas...') }}" required maxlength="140">
                    </label>

                    <label>
                        Título do card do banner
                        <input name="hero_card_title" value="{{ old('hero_card_title', $municipality->hero_card_title ?? 'Sua experiência, em movimento') }}" required maxlength="100">
                    </label>
                </div>

                <label>
                    Tags do card, separadas por vírgula
                    <input name="hero_card_tags" value="{{ old('hero_card_tags', implode(', ', $municipality->hero_card_tags ?? ['4 horas', 'Família', 'Cultura'])) }}" maxlength="160">
                </label>

                <hr class="admin-form__divider">

                <div class="admin-form__grid">
                    <label>
                        Chamada pequena da Economia local
                        <input name="local_economy_eyebrow" value="{{ old('local_economy_eyebrow', $municipality->local_economy_eyebrow ?? 'Economia local') }}" required maxlength="80">
                    </label>

                    <label>
                        Título da Economia local
                        <input name="local_economy_title" value="{{ old('local_economy_title', $municipality->local_economy_title ?? 'Cada rota também movimenta o território') }}" required maxlength="120">
                    </label>
                </div>

                <label>
                    Texto da Economia local
                    <textarea name="local_economy_description" rows="4" required maxlength="360">{{ old('local_economy_description', $municipality->local_economy_description ?? 'Pequenos negócios, experiências culturais e lugares menos conhecidos passam a fazer parte do percurso de forma relevante — nunca como publicidade invasiva.') }}</textarea>
                </label>

                <div class="admin-form__grid">
                    <label>
                        Destaque curto
                        <input name="local_economy_stat" value="{{ old('local_economy_stat', $municipality->local_economy_stat ?? '+ oportunidades locais no caminho') }}" maxlength="120">
                    </label>

                    <label>
                        Texto do link
                        <input name="local_economy_link_label" value="{{ old('local_economy_link_label', $municipality->local_economy_link_label ?? 'Conheça quem faz a cidade') }}" maxlength="100">
                    </label>
                </div>

                <label>
                    URL do link
                    <input name="local_economy_link_url" value="{{ old('local_economy_link_url', $municipality->local_economy_link_url ?? '') }}" maxlength="255" placeholder="{{ route('guides.index') }}">
                </label>

                <div class="admin-form__grid">
                    <label>
                        Imagem da Economia local
                        <input name="local_economy_image" type="file" accept="image/*">
                    </label>

                    <label>
                        Texto alternativo da imagem
                        <input name="local_economy_image_alt" value="{{ old('local_economy_image_alt', $municipality->local_economy_image_alt ?? '') }}" maxlength="180" placeholder="Ex.: Artesã local trabalhando em uma peça">
                    </label>
                </div>

                @if ($municipality?->localEconomyImageUrl())
                    <div class="appearance-preview appearance-preview--wide">
                        <span>Imagem atual da Economia local</span>
                        <img src="{{ $municipality->localEconomyImageUrl() }}" alt="">
                    </div>
                @endif

                <div class="admin-form__actions">
                    <a href="{{ route('admin.dashboard') }}">Cancelar</a>
                    <button class="route-search__button" type="submit">
                        <span>Salvar aparência</span>
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
