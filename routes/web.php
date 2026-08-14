<?php

use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\ItineraryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\AdaptationController;

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
    'tourist-spots' => [
        'uri' => 'pontos-turisticos',
        'title' => 'Pontos turísticos',
        'eyebrow' => 'Atrativos oficiais',
        'description' => 'Lugares que revelam a natureza, a história e a identidade do município.',
        'items' => ['Centro de Cultura e Memória', 'Mirante do Encontro', 'Praça da Matriz'],
    ],
    'culture' => [
        'uri' => 'historia-e-cultura',
        'title' => 'História e cultura',
        'eyebrow' => 'Identidade territorial',
        'description' => 'Histórias, tradições, patrimônios e vozes que mantêm viva a memória local.',
        'items' => ['Formação do município', 'Festas e tradições', 'Vozes do Território'],
    ],
    'tours' => [
        'uri' => 'passeios',
        'title' => 'Passeios e experiências',
        'eyebrow' => 'Atividades locais',
        'description' => 'Passeios, oficinas e vivências organizadas por pessoas do território.',
        'items' => ['Caminhada pelo centro histórico', 'Passeio de barco', 'Oficina de artesanato'],
    ],
    'guides' => [
        'uri' => 'guias',
        'title' => 'Guias turísticos',
        'eyebrow' => 'Profissionais validados',
        'description' => 'Encontre profissionais preparados para apresentar o território com responsabilidade.',
        'items' => ['Guia de cultura e memória', 'Guia de natureza', 'Condutor de experiências locais'],
    ],
    'official-itineraries' => [
        'uri' => 'roteiros-oficiais',
        'title' => 'Roteiros oficiais',
        'eyebrow' => 'Curadoria municipal',
        'description' => 'Percursos prontos para conhecer diferentes aspectos da cidade.',
        'items' => ['Entre cultura e tranquilidade', 'Sabores do território', 'Natureza e paisagens'],
    ],
    'stays' => [
        'uri' => 'onde-ficar',
        'title' => 'Onde ficar',
        'eyebrow' => 'Hospedagens',
        'description' => 'Hotéis, pousadas e hospedagens com informações validadas.',
        'items' => ['Pousada Centro Histórico', 'Hospedagem da Enseada', 'Chalés da Serra'],
    ],
    'dining' => [
        'uri' => 'onde-comer',
        'title' => 'Onde comer',
        'eyebrow' => 'Gastronomia local',
        'description' => 'Restaurantes, mercados e produtores que expressam os sabores da cidade.',
        'items' => ['Mercado de Sabores Locais', 'Cozinha da Praça', 'Café do Casario'],
    ],
    'agenda' => [
        'uri' => 'agenda',
        'title' => 'Agenda',
        'eyebrow' => 'Acontece na cidade',
        'description' => 'Eventos, festas, apresentações e atividades com data marcada.',
        'items' => ['Feira cultural', 'Festival de sabores', 'Apresentação na praça'],
    ],
];

foreach ($catalogs as $routePrefix => $catalog) {
    Route::view('/' . $catalog['uri'], 'pages.catalog', [
        ...$catalog,
        'routePrefix' => $routePrefix,
    ])->name($routePrefix . '.index');

    Route::get('/' . $catalog['uri'] . '/{slug}', [CatalogController::class, 'show'])
        ->defaults('catalog', $catalog)
        ->defaults('routePrefix', $routePrefix)
        ->name($routePrefix . '.show');
}

Route::view('/mapa-da-cidade', 'pages.map')->name('city-map');
Route::get('/criar-rota', [ItineraryController::class, 'create'])
    ->name('routes.create');

Route::post('/criar-rota', [ItineraryController::class, 'store'])
    ->name('routes.store');
Route::get(
    '/minha-rota/{itinerary}',
    [ItineraryController::class, 'show']
)->name('routes.show');
Route::post(
    '/minha-rota/{itinerary}/adaptar/chuva',
    [AdaptationController::class, 'rain']
)->name('routes.adapt.rain');

Route::get(
    '/minha-rota/{itinerary}/adaptacoes/{adaptation}',
    [AdaptationController::class, 'show']
)->name('routes.adaptation.show');
Route::view('/entrar', 'pages.login')->name('login');

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

Route::middleware(['auth', 'can:access-admin-panel'])->prefix('gestor')->name('admin.')->group(function (): void {
    Route::view('/', 'admin.dashboard')->name('dashboard');

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
        Route::view('/' . $name, 'admin.module', compact('title'))->name($name . '.index');
    }
});
