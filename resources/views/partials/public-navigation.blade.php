<div class="collapse navbar-collapse" id="main-navigation">
    <div class="navigation-actions">
        <a class="route-cta" href="{{ route('routes.create') }}">Criar minha rota</a>

        @auth
            @can('manage-platform')
                <a class="manager-link" href="{{ route('platform.dashboard') }}">
                    <span>Plataforma</span>
                </a>
            @endcan

            @can('access-admin-panel')
                <a class="manager-link" href="{{ route('admin.dashboard') }}">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 21v-2a7 7 0 0 1 14 0v2Z"></path></svg>
                    <span>Área do gestor</span>
                </a>
            @endcan

            <form action="{{ route('logout') }}" method="post" class="logout-form">
                @csrf
                <button class="manager-link manager-link--ghost" type="submit">
                    <span>Sair</span>
                </button>
            </form>
        @endauth
    </div>

    <ul class="navbar-nav mx-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="{{ route('discover') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Descubra</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('discover') }}">Visão geral</a></li>
                <li><a class="dropdown-item" href="{{ route('tourist-spots.index') }}">Pontos turísticos</a></li>
                <li><a class="dropdown-item" href="{{ route('culture.index') }}">História e cultura</a></li>
                <li><a class="dropdown-item" href="{{ route('city-map') }}">Mapa da cidade</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="{{ route('experiences') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Experiências</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('experiences') }}">Visão geral</a></li>
                <li><a class="dropdown-item" href="{{ route('tours.index') }}">Passeios</a></li>
                <li><a class="dropdown-item" href="{{ route('guides.index') }}">Guias turísticos</a></li>
                <li><a class="dropdown-item" href="{{ route('official-itineraries.index') }}">Roteiros oficiais</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="{{ route('agenda.index') }}">Agenda</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('stays.index') }}">Onde ficar</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('dining.index') }}">Onde comer</a></li>
    </ul>
</div>
