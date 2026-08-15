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

            @if ($errors->any())
                <div class="route-builder-error" role="alert">

                    <span class="route-builder-error__icon" aria-hidden="true">
                        !
                    </span>

                    <div>

                        <strong>
                            Ajuste sua busca para continuar.
                        </strong>

                        <ul class="route-builder-error__list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>

                </div>
            @endif

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

            @if (session('needs_time_question'))
                <div class="route-builder-followup" role="status" aria-live="polite">
                    <p class="eyebrow">Só preciso de mais uma informação</p>
                    <h2>Quanto tempo você tem para essa experiência?</h2>
                    <p>
                        O tempo muda totalmente a montagem da rota. Com essa resposta, conseguimos escolher uma sequência
                        mais realista sem pedir todos os detalhes de uma vez.
                    </p>
                    <div class="route-builder-time-options" aria-label="Escolha o tempo disponível">
                        <button type="button" class="route-builder-time-option"
                            data-time-answer="Tenho até 2 horas disponíveis.">
                            Até 2 horas
                        </button>
                        <button type="button" class="route-builder-time-option"
                            data-time-answer="Tenho entre 2 e 4 horas disponíveis.">
                            2 a 4 horas
                        </button>
                        <button type="button" class="route-builder-time-option"
                            data-time-answer="Tenho mais de 4 horas disponíveis.">
                            Mais de 4 horas
                        </button>
                    </div>
                </div>
            @endif

            @if (session('needs_budget_question'))
                <div class="route-builder-followup" role="status" aria-live="polite">
                    <p class="eyebrow">Só preciso de mais uma informação</p>
                    <h2>Quanto você quer gastar nessa experiência?</h2>
                    <p>
                        Como sua busca envolve custos, essa resposta ajuda a manter toda a rota dentro do valor que faz
                        sentido para você.
                    </p>
                    <div class="route-builder-time-options" aria-label="Escolha o orçamento disponível">
                        <button type="button" class="route-builder-time-option"
                            data-budget-answer="Quero somente opções gratuitas.">
                            Somente gratuito
                        </button>
                        <button type="button" class="route-builder-time-option"
                            data-budget-answer="Meu orçamento máximo é de R$ 50.">
                            Até R$ 50
                        </button>
                        <button type="button" class="route-builder-time-option"
                            data-budget-answer="Meu orçamento máximo é de R$ 150.">
                            Até R$ 150
                        </button>
                        <button type="button" class="route-builder-time-option"
                            data-budget-answer="Não tenho limite de orçamento definido.">
                            Sem limite definido
                        </button>
                    </div>
                </div>
            @endif

            @if (session('route_error'))
                <div class="route-builder-empty-state" role="status">
                    <p class="eyebrow">Vamos ajustar sua busca</p>
                    <h2>Não encontramos uma rota completa ainda</h2>
                    <p>{{ session('route_error') }}</p>

                    @if (session('route_suggestions'))
                        <div class="route-builder-suggestions">
                            <strong>Você pode tentar:</strong>
                            <ul>
                                @foreach (session('route_suggestions', []) as $suggestion)
                                    <li>{{ $suggestion }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('route_alternatives'))
                        <div class="route-builder-alternatives">
                            <strong>Alternativas próximas do que você pediu</strong>
                            <div class="route-builder-alternatives__grid">
                                @foreach (session('route_alternatives', []) as $alternative)
                                    <article class="route-builder-alternative">
                                        @if (! empty($alternative['image_url']))
                                            <img src="{{ $alternative['image_url'] }}" alt="">
                                        @endif
                                        <div>
                                            <span>{{ $alternative['category'] }}</span>
                                            <h3>{{ $alternative['name'] }}</h3>
                                            <p>{{ $alternative['description'] }}</p>
                                            <small>
                                                {{ $alternative['duration'] }} min ·
                                                {{ (float) $alternative['cost'] > 0 ? 'R$ '.number_format((float) $alternative['cost'], 2, ',', '.') : 'Gratuito' }}
                                            </small>
                                            <a href="{{ route('tourist-spots.show', $alternative['slug']) }}">
                                                Ver detalhes
                                                <span aria-hidden="true">→</span>
                                            </a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <form action="{{ route('routes.store') }}" method="post" data-route-builder-form
                data-route-preferences-form
                data-loading-label="Criando sua rota..."
                data-loading-title="Criando sua experiência personalizada"
                data-loading-description="Estamos avaliando tempo, orçamento, perfil e atrativos oficiais do município.">
                @csrf
                <input type="hidden" name="time_confirmed" value="{{ old('time_confirmed') }}" data-time-confirmed>
                <input type="hidden" name="budget_confirmed" value="{{ old('budget_confirmed') }}" data-budget-confirmed>
                <label for="experience-query">Conte o que você
                    procura</label>
                <textarea id="experience-query" name="description" rows="7"
                    @error('description') aria-invalid="true" aria-describedby="experience-query-error" @enderror
                    placeholder="Ex.: Quero cultura e tranquilidade, estou com uma criança e tenho quatro horas.">{{ old('description', $initialQuery ?? '') }}</textarea>
                @error('description')
                    <p class="field-error" id="experience-query-error">{{ $message }}</p>
                @enderror
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
