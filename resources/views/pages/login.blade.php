@extends('layouts.public')
@section('title', 'Entrar')
@section('content')
    <section class="page-section"><div class="page-container login-placeholder"><p class="eyebrow">Acesso restrito</p><h1>Área do gestor</h1><p>A autenticação administrativa será conectada nesta página. O painel já está protegido por autenticação e permissão.</p><a class="text-link" href="{{ route('home') }}">← Voltar para a página inicial</a></div></section>
@endsection
