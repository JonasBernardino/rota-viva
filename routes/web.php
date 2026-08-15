<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\AdaptationController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\CityMapController;
use App\Http\Controllers\Public\ItineraryController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::view('/descubra', 'pages.landing', [
    'eyebrow' => 'Explore o território',
    'title' => 'Descubra a cidade',
    'description' => 'Conheça lugares, histórias e paisagens validados pelo município.',
    'links' => [
        ['route' => 'tourist-spots.index', 'title' => 'Pontos turísticos', 'description' => 'Praias, igrejas, museus, praças, trilhas e patrimônios.'],
        ['route' => 'culture.index', 'title' => 'História e cultura', 'description' => 'Memórias, comunidades, tradições e expressões culturais.'],
        ['route' => 'city-map', 'title' => 'Mapa da cidade', 'description' => 'Visualize os principais locais e planeje seus deslocamentos.'],
    ],
])->name('discover');

Route::view('/experiencias', 'pages.landing', [
    'eyebrow' => 'Viva o território',
    'title' => 'Experiências',
    'description' => 'Atividades, profissionais e roteiros preparados para diferentes formas de viver a cidade.',
    'links' => [
        ['route' => 'tours.index', 'title' => 'Passeios', 'description' => 'Atividades guiadas, oficinas e experiências locais.'],
        ['route' => 'guides.index', 'title' => 'Guias turísticos', 'description' => 'Profissionais cadastrados e validados pelo município.'],
        ['route' => 'official-itineraries.index', 'title' => 'Roteiros oficiais', 'description' => 'Percursos temáticos selecionados pela gestão municipal.'],
    ],
])->name('experiences');

$catalogs = [
    'tourist-spots' => 'pontos-turisticos',
    'culture' => 'historia-e-cultura',
    'tours' => 'passeios',
    'guides' => 'guias',
    'official-itineraries' => 'roteiros-oficiais',
    'stays' => 'onde-ficar',
    'dining' => 'onde-comer',
    'agenda' => 'agenda',
];

foreach ($catalogs as $routePrefix => $uri) {
    Route::get('/'.$uri, [CatalogController::class, 'index'])
        ->defaults('section', $routePrefix)
        ->name($routePrefix.'.index');

    Route::get('/'.$uri.'/{slug}', [CatalogController::class, 'show'])
        ->defaults('section', $routePrefix)
        ->name($routePrefix.'.show');
}

Route::get('/mapa-da-cidade', CityMapController::class)->name('city-map');

Route::get('/criar-rota', [ItineraryController::class, 'create'])->name('routes.create');
Route::post('/criar-rota', [ItineraryController::class, 'store'])->name('routes.store');

Route::get('/minha-rota/{itinerary}', [ItineraryController::class, 'show'])->name('routes.show');
Route::post('/minha-rota/{itinerary}/adaptar/chuva', [AdaptationController::class, 'rain'])->name('routes.adapt.rain');
Route::get('/minha-rota/{itinerary}/adaptacoes/{adaptation}', [AdaptationController::class, 'show'])->name('routes.adaptation.show');

// Autenticação da gestão municipal
Route::redirect('/entrar', '/gestor/entrar');
Route::get('/gestor/entrar', [AuthController::class, 'showLogin'])->name('login');
Route::post('/gestor/entrar', [AuthController::class, 'login'])->name('login.post');
Route::post('/sair', [AuthController::class, 'logout'])->name('logout');

$institutionalPages = [
    'about' => ['/sobre-o-projeto', 'Sobre o projeto', 'Conheça a proposta, os princípios e o impacto esperado do Rota Viva.'],
    'contact' => ['/contato', 'Contato', 'Encontre os canais oficiais para falar com a gestão municipal.'],
    'accessibility.resources' => ['/acessibilidade/recursos', 'Recursos de acessibilidade', 'Conheça os recursos que ajudam cada pessoa a planejar sua visita.'],
    'accessibility.statement' => ['/acessibilidade/declaracao', 'Declaração de acessibilidade', 'Nosso compromisso com uma experiência digital e territorial inclusiva.'],
    'help' => ['/ajuda', 'Ajuda', 'Respostas e orientações para utilizar o Rota Viva.'],
    'privacy' => ['/privacidade', 'Privacidade', 'Entenda como os dados são tratados e protegidos.'],
    'terms' => ['/termos-de-uso', 'Termos de uso', 'Condições para utilizar os serviços do portal.'],
];

foreach ($institutionalPages as $name => [$uri, $title, $description]) {
    Route::view($uri, 'pages.content', compact('title', 'description'))->name($name);
}

// Painel Administrativo de Gestão Municipal
Route::middleware(['auth', 'can:access-admin-panel'])->prefix('gestor')->name('admin.')->group(function (): void {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    $modules = [
        'tourist-spots' => 'Pontos turísticos',
        'culture' => 'História e cultura',
        'establishments' => 'Estabelecimentos',
        'tours' => 'Passeios',
        'guides' => 'Guias turísticos',
        'events' => 'Eventos',
        'official-itineraries' => 'Roteiros oficiais',
    ];

    foreach ($modules as $name => $title) {
        Route::get('/'.$name, [AdminController::class, 'module'])
            ->defaults('module', $name)
            ->name($name.'.index');

        Route::get('/'.$name.'/novo', [AdminController::class, 'create'])
            ->defaults('module', $name)
            ->name($name.'.create');

        Route::post('/'.$name, [AdminController::class, 'store'])
            ->defaults('module', $name)
            ->name($name.'.store');

        Route::get('/'.$name.'/{id}/editar', [AdminController::class, 'edit'])
            ->defaults('module', $name)
            ->whereNumber('id')
            ->name($name.'.edit');

        Route::put('/'.$name.'/{id}', [AdminController::class, 'update'])
            ->defaults('module', $name)
            ->whereNumber('id')
            ->name($name.'.update');

        Route::delete('/'.$name.'/{id}', [AdminController::class, 'destroy'])
            ->defaults('module', $name)
            ->whereNumber('id')
            ->name($name.'.destroy');
    }
});
