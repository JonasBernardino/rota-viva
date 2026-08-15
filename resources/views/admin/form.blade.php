@extends('layouts.public')

@php
    $isEditing = filled($item);
    $action = $isEditing
        ? route('admin.'.$module.'.update', $item->id)
        : route('admin.'.$module.'.store');
    $placeMedia = $type === 'place' && $isEditing
        ? (($item->midias ?? collect())->firstWhere('is_destaque', true) ?? ($item->midias ?? collect())->first())
        : null;
    $placeMediaUrl = $placeMedia?->url;
    $currentImage = match ($type) {
        'place' => $placeMediaUrl,
        'business' => $item->imagem_capa ?? null,
        'event' => $item->imagem_url ?? null,
        default => null,
    };
    $currentImageUrl = $currentImage
        ? (\Illuminate\Support\Str::startsWith($currentImage, ['http://', 'https://', '/']) ? $currentImage : asset('storage/'.$currentImage))
        : null;
@endphp

@section('title', ($isEditing ? 'Editar ' : 'Novo cadastro — ') . $title)

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">
                <a href="{{ route('admin.'.$module.'.index') }}" style="color: inherit; text-decoration: none;">← {{ $title }}</a>
            </p>
            <h1>{{ $isEditing ? 'Editar cadastro' : 'Novo cadastro' }}</h1>
            <p>{{ $description }}</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container admin-form-shell">
            @if ($errors->any())
                <div class="route-builder-error" role="alert">
                    <span class="route-builder-error__icon" aria-hidden="true">!</span>
                    <div>
                        <strong>Revise os campos destacados.</strong>
                        <p>Algumas informações obrigatórias não foram preenchidas corretamente.</p>
                    </div>
                </div>
            @endif

            <form class="admin-form" action="{{ $action }}" method="post" enctype="multipart/form-data">
                @csrf

                @if ($isEditing)
                    @method('PUT')
                @endif

                @if ($type === 'place')
                    <div class="admin-form__grid">
                        <label>
                            Categoria
                            <select name="categoria_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('categoria_id', $item->categoria_id ?? 0) === $category->id)>
                                        {{ $category->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            Nome
                            <input name="nome" value="{{ old('nome', $item->nome ?? '') }}" required>
                        </label>

                        <label>
                            Slug
                            <input name="slug" value="{{ old('slug', $item->slug ?? '') }}" placeholder="gerado automaticamente se ficar vazio">
                        </label>

                        <label>
                            Intensidade
                            <select name="intensidade" required>
                                @foreach (['low' => 'Leve', 'medium' => 'Moderada', 'high' => 'Alta'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('intensidade', $item->intensidade ?? 'low') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            Latitude
                            <input name="latitude" type="number" step="0.0000001" value="{{ old('latitude', $item->latitude ?? '') }}" required>
                        </label>

                        <label>
                            Longitude
                            <input name="longitude" type="number" step="0.0000001" value="{{ old('longitude', $item->longitude ?? '') }}" required>
                        </label>

                        <label>
                            Duração em minutos
                            <input name="duracao_minutos" type="number" min="1" value="{{ old('duracao_minutos', $item->duracao_minutos ?? 60) }}" required>
                        </label>

                        <label>
                            Custo médio
                            <input name="custo_medio" type="number" min="0" step="0.01" value="{{ old('custo_medio', $item->custo_medio ?? 0) }}">
                        </label>
                    </div>

                    <label>
                        Descrição
                        <textarea name="descricao" rows="5">{{ old('descricao', $item->descricao ?? '') }}</textarea>
                    </label>

                    <label>
                        Tags separadas por vírgula
                        <input name="tags" value="{{ old('tags', isset($item) ? implode(', ', $item->tags ?? []) : '') }}">
                    </label>

                    <div class="admin-form__grid">
                        <label>
                            Imagem de destaque
                            <input name="imagem" type="file" accept="image/*">
                        </label>

                        <label>
                            Texto alternativo da imagem
                            <input name="imagem_alt" value="{{ old('imagem_alt', $placeMedia->descricao_acessibilidade ?? '') }}" maxlength="180" placeholder="Descreva a imagem para acessibilidade">
                        </label>
                    </div>

                    @if ($currentImageUrl)
                        <div class="appearance-preview appearance-preview--wide">
                            <span>Imagem atual</span>
                            <img src="{{ $currentImageUrl }}" alt="">
                        </div>
                    @endif

                    <div class="admin-form__checks">
                        <label><input type="checkbox" name="is_ar_livre" value="1" @checked(old('is_ar_livre', $item->is_ar_livre ?? false))> Ao ar livre</label>
                        <label><input type="checkbox" name="adequado_criancas" value="1" @checked(old('adequado_criancas', $item->adequado_criancas ?? true))> Adequado para crianças</label>
                        <label><input type="checkbox" name="is_disponivel" value="1" @checked(old('is_disponivel', $item->is_disponivel ?? true))> Disponível no portal</label>
                    </div>
                @elseif ($type === 'business')
                    <div class="admin-form__grid">
                        <label>
                            Nome
                            <input name="nome" value="{{ old('nome', $item->nome ?? '') }}" required>
                        </label>

                        <label>
                            Slug
                            <input name="slug" value="{{ old('slug', $item->slug ?? '') }}" placeholder="gerado automaticamente se ficar vazio">
                        </label>

                        <label>
                            Tipo
                            <select name="tipo_estabelecimento">
                                @foreach (['gastronomia' => 'Gastronomia', 'hospedagem' => 'Hospedagem', 'guia_turistico' => 'Guia turístico', 'atividade' => 'Passeio ou atividade', 'artesanato' => 'Artesanato'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('tipo_estabelecimento', $item->tipo_estabelecimento ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            Status
                            <select name="status_validacao" required>
                                @foreach (['pending' => 'Pendente', 'approved' => 'Aprovado', 'rejected' => 'Rejeitado', 'suspended' => 'Suspenso'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status_validacao', $item->status_validacao ?? 'approved') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            Endereço
                            <input name="endereco" value="{{ old('endereco', $item->endereco ?? '') }}">
                        </label>

                        <label>
                            Bairro
                            <input name="bairro" value="{{ old('bairro', $item->bairro ?? '') }}">
                        </label>

                        <label>
                            Latitude
                            <input name="latitude" type="number" step="0.0000001" value="{{ old('latitude', $item->latitude ?? '') }}">
                        </label>

                        <label>
                            Longitude
                            <input name="longitude" type="number" step="0.0000001" value="{{ old('longitude', $item->longitude ?? '') }}">
                        </label>

                        <label>
                            Telefone
                            <input name="telefone" value="{{ old('telefone', $item->telefone ?? '') }}">
                        </label>

                        <label>
                            WhatsApp
                            <input name="whatsapp" value="{{ old('whatsapp', $item->whatsapp ?? '') }}">
                        </label>

                        <label>
                            Instagram
                            <input name="instagram" value="{{ old('instagram', $item->instagram ?? '') }}">
                        </label>

                        <label>
                            Website
                            <input name="website" type="url" value="{{ old('website', $item->website ?? '') }}">
                        </label>

                        <label>
                            Faixa de preço
                            <select name="faixa_preco" required>
                                @foreach (['$' => '$', '$$' => '$$', '$$$' => '$$$', '$$$$' => '$$$$'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('faixa_preco', $item->faixa_preco ?? '$$') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <label>
                        Descrição
                        <textarea name="descricao" rows="5" required>{{ old('descricao', $item->descricao ?? '') }}</textarea>
                    </label>

                    <label>
                        Imagem de destaque
                        <input name="imagem" type="file" accept="image/*">
                    </label>

                    @if ($currentImageUrl)
                        <div class="appearance-preview appearance-preview--wide">
                            <span>Imagem atual</span>
                            <img src="{{ $currentImageUrl }}" alt="">
                        </div>
                    @endif

                    <div class="admin-form__checks">
                        <label><input type="checkbox" name="tem_selo_qualidade" value="1" @checked(old('tem_selo_qualidade', $item->tem_selo_qualidade ?? true))> Possui Selo de Qualidade</label>
                    </div>
                @elseif ($type === 'event')
                    <div class="admin-form__grid">
                        <label>
                            Nome
                            <input name="nome" value="{{ old('nome', $item->nome ?? '') }}" required>
                        </label>

                        <label>
                            Slug
                            <input name="slug" value="{{ old('slug', $item->slug ?? '') }}" placeholder="gerado automaticamente se ficar vazio">
                        </label>

                        <label>
                            Local
                            <input name="nome_local" value="{{ old('nome_local', $item->nome_local ?? '') }}" required>
                        </label>

                        <label>
                            Categoria
                            <input name="categoria" value="{{ old('categoria', $item->categoria ?? 'cultural') }}" required>
                        </label>

                        <label>
                            Início
                            <input name="inicia_em" type="datetime-local" value="{{ old('inicia_em', isset($item?->inicia_em) ? $item->inicia_em->format('Y-m-d\TH:i') : '') }}" required>
                        </label>

                        <label>
                            Término
                            <input name="termina_em" type="datetime-local" value="{{ old('termina_em', isset($item?->termina_em) ? $item->termina_em->format('Y-m-d\TH:i') : '') }}">
                        </label>

                        <label>
                            Endereço
                            <input name="endereco" value="{{ old('endereco', $item->endereco ?? '') }}">
                        </label>

                        <label>
                            Status
                            <select name="status" required>
                                @foreach (['scheduled' => 'Agendado', 'draft' => 'Rascunho', 'cancelled' => 'Cancelado', 'finished' => 'Finalizado'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $item->status ?? 'scheduled') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            Latitude
                            <input name="latitude" type="number" step="0.0000001" value="{{ old('latitude', $item->latitude ?? '') }}">
                        </label>

                        <label>
                            Longitude
                            <input name="longitude" type="number" step="0.0000001" value="{{ old('longitude', $item->longitude ?? '') }}">
                        </label>

                        <label>
                            Preço
                            <input name="preco" type="number" min="0" step="0.01" value="{{ old('preco', $item->preco ?? '') }}">
                        </label>

                        <label>
                            Capacidade
                            <input name="capacidade" type="number" min="1" value="{{ old('capacidade', $item->capacidade ?? '') }}">
                        </label>

                        <label>
                            Organizador
                            <input name="organizador" value="{{ old('organizador', $item->organizador ?? '') }}">
                        </label>
                    </div>

                    <label>
                        Descrição
                        <textarea name="descricao" rows="5" required>{{ old('descricao', $item->descricao ?? '') }}</textarea>
                    </label>

                    <label>
                        Imagem de destaque
                        <input name="imagem" type="file" accept="image/*">
                    </label>

                    @if ($currentImageUrl)
                        <div class="appearance-preview appearance-preview--wide">
                            <span>Imagem atual</span>
                            <img src="{{ $currentImageUrl }}" alt="">
                        </div>
                    @endif

                    <div class="admin-form__checks">
                        <label><input type="checkbox" name="is_gratuito" value="1" @checked(old('is_gratuito', $item->is_gratuito ?? true))> Gratuito</label>
                        <label><input type="checkbox" name="is_acessivel" value="1" @checked(old('is_acessivel', $item->is_acessivel ?? true))> Acessível</label>
                    </div>
                @else
                    <div class="admin-form__grid">
                        <label>
                            Título
                            <input name="titulo" value="{{ old('titulo', $item->titulo ?? '') }}" required>
                        </label>

                        <label>
                            Status
                            <select name="status" required>
                                @foreach (['official' => 'Oficial', 'draft' => 'Rascunho', 'ACTIVE' => 'Ativo'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $item->status ?? 'official') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            Duração total em minutos
                            <input name="duracao_total_minutos" type="number" min="1" value="{{ old('duracao_total_minutos', $item->duracao_total_minutos ?? 120) }}" required>
                        </label>

                        <label>
                            Custo estimado
                            <input name="custo_total_estimado" type="number" min="0" step="0.01" value="{{ old('custo_total_estimado', $item->custo_total_estimado ?? 0) }}">
                        </label>
                    </div>

                    <label>
                        Resumo
                        <textarea name="resumo" rows="5" required>{{ old('resumo', $item->resumo ?? '') }}</textarea>
                    </label>
                @endif

                <div class="admin-form__actions">
                    <a class="text-link" href="{{ route('admin.'.$module.'.index') }}">Cancelar</a>
                    <button class="route-cta" type="submit">{{ $isEditing ? 'Salvar alterações' : 'Criar cadastro' }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
