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

            @include('partials.ai-disclaimer')

            @if (session('ai_error'))
                <div class="route-builder-error" role="alert">

                    <span class="route-builder-error__icon" aria-hidden="true">
                        !
                    </span>

                    <div>

                        <strong>
                            Não conseguimos criar sua rota.
                        </strong>

                        <p>
                            {{ session('ai_error') }}
                        </p>

                    </div>

                </div>
            @endif
            <form action="{{ route('routes.store') }}" method="post" data-route-builder-form>
                @csrf
                <label for="experience-query">Conte o que você
                    procura</label>
                <textarea id="experience-query" name="description" rows="7"
                    placeholder="Ex.: Quero cultura e tranquilidade, estou com uma criança e tenho quatro horas.">{{ old('description', $initialQuery ?? '') }}</textarea>
                <p class="route-builder-status" data-route-builder-status hidden role="status">
                    Estamos criando sua rota. Se a IA local demorar, usamos uma interpretação segura pelo próprio sistema.
                </p>
                <button class="route-cta" type="submit" data-route-builder-submit>
                    <span data-route-builder-submit-label>Continuar</span>
                </button>
            </form>
        </div>
    </section>

    <div class="route-builder-loading" data-route-builder-loading hidden role="status" aria-live="polite">
        <div class="route-builder-loading__panel">
            <span class="route-builder-loading__spinner" aria-hidden="true"></span>
            <p class="eyebrow">Rota Viva em movimento</p>
            <strong>Criando sua experiência personalizada</strong>
            <span>Estamos avaliando tempo, orçamento, perfil e atrativos oficiais do município.</span>
        </div>
    </div>
@endsection
