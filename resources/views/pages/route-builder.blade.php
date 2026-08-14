@extends('layouts.public')
@section('title', 'Criar minha rota')
@section('description', 'Conte como deseja viver a cidade e receba uma rota personalizada.')
@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">Rota adaptativa</p>
            <h1>Como você quer viver a cidade hoje?</h1>
            <p>Esta será a jornada de preferências que alimentará o motor de rotas.</p>
        </div>
    </section>
    <section class="page-section">
        <div class="page-container route-builder-card">
            <ol class="route-builder-steps">
                <li><span>01</span>Seu momento</li>
                <li><span>02</span>Tempo e orçamento</li>
                <li><span>03</span>Companhia e mobilidade</li>
                <li><span>04</span>Sua experiência</li>
            </ol>
            <form action="{{ route('routes.store') }}" method="post">
                @csrf
                <label for="experience-query">Conte o que você
                    procura</label>
                <textarea 
                    id="experience-query" 
                    name="description" 
                    rows="7"
                    placeholder="Ex.: Quero cultura e tranquilidade, estou com uma criança e tenho quatro horas."
                    >
                    {{ old('description', $initialQuery ?? '') }}
                </textarea>
                <button class="route-cta" type="submit">
                    Continuar
                </button>
            </form>
        </div>
    </section>
@endsection

{{-- 
@extends('layouts.public')

@section('title', 'Criar minha rota')

@section('description',
    'Conte como deseja viver a cidade e receba uma rota personalizada.'
)

@section('content')

<section class="page-hero page-hero--compact">
    <div class="page-container page-hero__content">

        <p class="eyebrow">
            Rota adaptativa
        </p>

        <h1>
            Como você quer viver a cidade hoje?
        </h1>

        <p>
            Conte do seu jeito. O Rota Viva entende suas preferências
            e cria uma experiência compatível com seu momento.
        </p>

    </div>
</section>

<section class="page-section">

    <div class="page-container route-builder-card">

        <div class="route-builder-intro">

            <span class="route-builder-ai-badge">
                ✦ Experiência inteligente
            </span>

            <h2>
                Conte o que você procura
            </h2>

            <p>
                Você pode falar sobre interesses, tempo disponível,
                orçamento, companhia, mobilidade ou qualquer necessidade
                importante.
            </p>

        </div>

        <form
            action="{{ route('routes.store') }}"
            method="post"
        >

            @csrf

            <label
                class="visually-hidden"
                for="experience-query"
            >
                Descreva sua experiência
            </label>

            <textarea
                id="experience-query"
                name="description"
                rows="7"
                required
                placeholder="Ex.: Quero uma experiência tranquila e cultural, estou com uma criança, tenho quatro horas e orçamento de R$ 150."
            >{{ old('description', $initialQuery ?? '') }}</textarea>

            <div class="route-builder-examples">

                <span>Experimente dizer:</span>

                <button type="button">
                    Quero natureza sem caminhar muito
                </button>

                <button type="button">
                    Tenho só duas horas e quero conhecer a história da cidade
                </button>

                <button type="button">
                    Estou com crianças e quero gastar até R$ 100
                </button>

            </div>

            <button
                class="route-cta"
                type="submit"
            >
                <span>Criar minha rota</span>
                <span aria-hidden="true">→</span>
            </button>

        </form>

    </div>

</section>

@endsection --}}