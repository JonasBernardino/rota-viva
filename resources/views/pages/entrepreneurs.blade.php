@extends('layouts.public')

@section('title', 'Cadastrar empreendimento — Rota Viva')
@section('description', 'Solicite a inclusão de um empreendimento, guia, atividade ou produção cultural na base municipal do Rota Viva.')

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">Economia local</p>
            <h1>Cadastre seu empreendimento</h1>
            <p>Solicite a inclusão na base municipal. A publicação acontece somente após validação da gestão.</p>
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
                        <strong>Revise as informações enviadas.</strong>
                        <p>Alguns campos obrigatórios precisam de ajuste antes do envio.</p>
                    </div>
                </div>
            @endif

            <div class="privacy-note" role="note">
                <strong>Validação municipal antes da publicação</strong>
                <p>Seu cadastro será salvo como pendente. A equipe municipal poderá aprovar, rejeitar, suspender ou solicitar complementação. Dados de contato do responsável são usados apenas para análise cadastral.</p>
            </div>

            <form class="admin-form" action="{{ route('entrepreneurs.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="admin-form__grid">
                    <label>
                        Nome público do empreendimento
                        <input name="nome" value="{{ old('nome') }}" required maxlength="255">
                    </label>

                    <label>
                        Tipo
                        <select name="tipo_estabelecimento" required>
                            @foreach ([
                                'hospedagem' => 'Hospedagem',
                                'gastronomia' => 'Gastronomia',
                                'atividade' => 'Passeio ou atividade',
                                'guia_turistico' => 'Guia turístico',
                                'artesanato' => 'Artesanato',
                                'produtor_cultural' => 'Produtor cultural',
                                'outro' => 'Outro serviço turístico',
                            ] as $value => $label)
                                <option value="{{ $value }}" @selected(old('tipo_estabelecimento') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <label>
                    Descrição
                    <textarea name="descricao" rows="5" required maxlength="1200" placeholder="Conte o que você oferece, diferenciais, horários ou orientações importantes.">{{ old('descricao') }}</textarea>
                </label>

                <div class="admin-form__grid">
                    <label>
                        Endereço
                        <input name="endereco" value="{{ old('endereco') }}" maxlength="255">
                    </label>

                    <label>
                        Bairro
                        <input name="bairro" value="{{ old('bairro') }}" maxlength="255">
                    </label>
                </div>

                <div class="admin-form__grid">
                    <label>
                        Telefone
                        <input name="telefone" value="{{ old('telefone') }}" maxlength="40">
                    </label>

                    <label>
                        WhatsApp
                        <input name="whatsapp" value="{{ old('whatsapp') }}" maxlength="40">
                    </label>
                </div>

                <div class="admin-form__grid">
                    <label>
                        Instagram
                        <input name="instagram" value="{{ old('instagram') }}" maxlength="80" placeholder="@seuperfil">
                    </label>

                    <label>
                        Site
                        <input name="website" value="{{ old('website') }}" maxlength="255" placeholder="https://...">
                    </label>
                </div>

                <div class="admin-form__grid">
                    <label>
                        Faixa de preço
                        <select name="faixa_preco" required>
                            @foreach (['$' => 'Econômico', '$$' => 'Moderado', '$$$' => 'Premium', '$$$$' => 'Especial'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('faixa_preco', '$$') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        Foto de apresentação
                        <input name="imagem" type="file" accept="image/jpeg,image/png,image/webp">
                    </label>
                </div>

                <hr class="admin-form__divider">

                <div class="admin-form__grid">
                    <label>
                        Nome do responsável
                        <input name="responsavel_nome" value="{{ old('responsavel_nome') }}" required maxlength="255">
                    </label>

                    <label>
                        E-mail do responsável
                        <input name="responsavel_email" type="email" value="{{ old('responsavel_email') }}" required maxlength="255">
                    </label>
                </div>

                <div class="admin-form__checks">
                    <label>
                        <input name="aceite_privacidade" type="checkbox" value="1" required @checked(old('aceite_privacidade'))>
                        <span>Li e aceito que os dados sejam usados para análise cadastral municipal, conforme a <a href="{{ route('privacy') }}">Política de Privacidade</a>.</span>
                    </label>
                </div>

                <div class="admin-form__actions">
                    <a href="{{ route('home') }}">Cancelar</a>
                    <button class="route-search__button" type="submit">
                        <span>Enviar solicitação</span>
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
