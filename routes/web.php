<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MunicipalityAppearanceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Platform\PlatformController;
use App\Http\Controllers\Public\AdaptationController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\CityMapController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ItineraryController;
use App\Http\Controllers\Public\MunicipalityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)
    ->name('home');

Route::get('/municipios/selecionar', MunicipalityController::class)
    ->name('municipalities.select');

/*
|--------------------------------------------------------------------------
| Descubra
|--------------------------------------------------------------------------
*/

Route::view('/descubra', 'pages.landing', [
    'eyebrow' => 'Explore o território',

    'title' => 'Descubra a cidade',

    'description' => 'Conheça lugares, histórias e paisagens validados pelo município.',

    'links' => [
        [
            'route' => 'tourist-spots.index',
            'title' => 'Pontos turísticos',
            'description' => 'Praias, igrejas, museus, praças, trilhas e patrimônios.',
        ],

        [
            'route' => 'culture.index',
            'title' => 'História e cultura',
            'description' => 'Memórias, comunidades, tradições e expressões culturais.',
        ],

        [
            'route' => 'city-map',
            'title' => 'Mapa da cidade',
            'description' => 'Visualize os principais locais e planeje seus deslocamentos.',
        ],
    ],
])->name('discover');

/*
|--------------------------------------------------------------------------
| Experiências
|--------------------------------------------------------------------------
*/

Route::view('/experiencias', 'pages.landing', [
    'eyebrow' => 'Viva o território',

    'title' => 'Experiências',

    'description' => 'Atividades, profissionais e roteiros preparados para diferentes formas de viver a cidade.',

    'links' => [
        [
            'route' => 'tours.index',
            'title' => 'Passeios',
            'description' => 'Atividades guiadas, oficinas e experiências locais.',
        ],

        [
            'route' => 'guides.index',
            'title' => 'Guias turísticos',
            'description' => 'Profissionais cadastrados e validados pelo município.',
        ],

        [
            'route' => 'official-itineraries.index',
            'title' => 'Roteiros oficiais',
            'description' => 'Percursos temáticos selecionados pela gestão municipal.',
        ],
    ],
])->name('experiences');

/*
|--------------------------------------------------------------------------
| Catálogos públicos
|--------------------------------------------------------------------------
*/

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
    Route::get(
        '/'.$uri,
        [CatalogController::class, 'index']
    )
        ->defaults('section', $routePrefix)
        ->name($routePrefix.'.index');

    Route::get(
        '/'.$uri.'/{slug}',
        [CatalogController::class, 'show']
    )
        ->defaults('section', $routePrefix)
        ->name($routePrefix.'.show');
}

/*
|--------------------------------------------------------------------------
| Mapa público
|--------------------------------------------------------------------------
*/

Route::get(
    '/mapa-da-cidade',
    CityMapController::class
)->name('city-map');

/*
|--------------------------------------------------------------------------
| Criação de roteiro
|--------------------------------------------------------------------------
*/

Route::get(
    '/criar-rota',
    [ItineraryController::class, 'create']
)->name('routes.create');

Route::post(
    '/criar-rota',
    [ItineraryController::class, 'store']
)
    ->middleware('throttle:route-generation')
    ->name('routes.store');

/*
|--------------------------------------------------------------------------
| Resultado do roteiro
|--------------------------------------------------------------------------
|
| Apesar do Model ter sido renomeado para Roteiro,
| podemos continuar utilizando {itinerary}.
|
| O importante é que o método do Controller esteja:
|
| public function show(Roteiro $itinerary)
|
*/

Route::get(
    '/minha-rota/{itinerary}',
    [ItineraryController::class, 'show']
)->name('routes.show');

/*
|--------------------------------------------------------------------------
| Adaptação do roteiro
|--------------------------------------------------------------------------
|
| Mesma lógica:
|
| AdaptationController deverá receber:
|
| Roteiro $itinerary
| AdaptacaoRota $adaptation
|
*/

Route::post(
    '/minha-rota/{itinerary}/adaptar/chuva',
    [AdaptationController::class, 'rain']
)
    ->middleware('throttle:route-adaptation')
    ->name('routes.adapt.rain');

Route::get(
    '/minha-rota/{itinerary}/adaptacoes/{adaptation}',
    [AdaptationController::class, 'show']
)->name('routes.adaptation.show');

/*
|--------------------------------------------------------------------------
| Autenticação da gestão municipal
|--------------------------------------------------------------------------
*/

Route::redirect(
    '/entrar',
    '/gestor/entrar'
);

Route::get(
    '/gestor/entrar',
    [AuthController::class, 'showLogin']
)->name('login');

Route::post(
    '/gestor/entrar',
    [AuthController::class, 'login']
)
    ->middleware('throttle:login')
    ->name('login.post');

Route::post(
    '/sair',
    [AuthController::class, 'logout']
)->name('logout');

/*
|--------------------------------------------------------------------------
| Páginas institucionais
|--------------------------------------------------------------------------
*/

// Painel da plataforma
Route::middleware(['auth', 'can:manage-platform'])->prefix('plataforma')->name('platform.')->group(function (): void {
    Route::get('/', [PlatformController::class, 'dashboard'])->name('dashboard');
    Route::get('/municipios/novo', [PlatformController::class, 'createMunicipality'])->name('municipalities.create');
    Route::post('/municipios', [PlatformController::class, 'storeMunicipality'])
        ->middleware('throttle:admin-actions')
        ->name('municipalities.store');
});

$institutionalPages = [
    'about' => [
        '/sobre-o-projeto',
        'Sobre o projeto',
        'Conheça a proposta, os princípios e o impacto esperado do Rota Viva.',
    ],

    'contact' => [
        '/contato',
        'Contato',
        'Encontre os canais oficiais para falar com a gestão municipal.',
    ],

    'accessibility.resources' => [
        '/acessibilidade/recursos',
        'Recursos de acessibilidade',
        'Conheça os recursos que ajudam cada pessoa a planejar sua visita.',
    ],

    'accessibility.statement' => [
        '/acessibilidade/declaracao',
        'Declaração de acessibilidade',
        'Nosso compromisso com uma experiência digital e territorial inclusiva.',
    ],

    'help' => [
        '/ajuda',
        'Ajuda',
        'Respostas e orientações para utilizar o Rota Viva.',
    ],

    'privacy' => [
        '/privacidade',
        'Privacidade',
        'Entenda como os dados são tratados e protegidos.',
    ],

    'terms' => [
        '/termos-de-uso',
        'Termos de uso',
        'Condições para utilizar os serviços do portal.',
    ],
];

foreach (
    $institutionalPages as $name => [$uri, $title, $description]
) {
    Route::view(
        $uri,
        'pages.content',
        compact(
            'title',
            'description'
        )
    )->name($name);
}

/*
|--------------------------------------------------------------------------
| Painel Administrativo de Gestão Municipal
|--------------------------------------------------------------------------
|
| DashboardController
|
| - indicadores operacionais;
| - comportamento dos visitantes;
| - demanda não atendida;
| - mapa de calor.
|
| AdminController
|
| - CRUD dos módulos administrativos.
|
*/

Route::middleware([
    'auth',
    'can:access-admin-panel',
])
    ->prefix('gestor')
    ->name('admin.')
    ->group(function (): void {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [DashboardController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/aparencia',
            [MunicipalityAppearanceController::class, 'edit']
        )->name('appearance.edit');

        Route::put(
            '/aparencia',
            [MunicipalityAppearanceController::class, 'update']
        )
            ->middleware('throttle:admin-actions')
            ->name('appearance.update');

        /*
        |--------------------------------------------------------------------------
        | Módulos administrativos
        |--------------------------------------------------------------------------
        */

        $modules = [
            'tourist-spots' => 'Pontos turísticos',

            'culture' => 'História e cultura',

            'establishments' => 'Estabelecimentos',

            'tours' => 'Passeios',

            'guides' => 'Guias turísticos',

            'events' => 'Eventos',

            'official-itineraries' => 'Roteiros oficiais',
        ];

        foreach (
            $modules as $name => $title
        ) {
            /*
            |--------------------------------------------------------------------------
            | Listagem
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/'.$name,
                [
                    AdminController::class,
                    'module',
                ]
            )
                ->defaults(
                    'module',
                    $name
                )
                ->name(
                    $name.'.index'
                );

            /*
            |--------------------------------------------------------------------------
            | Formulário de criação
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/'.$name.'/novo',
                [
                    AdminController::class,
                    'create',
                ]
            )
                ->defaults(
                    'module',
                    $name
                )
                ->name(
                    $name.'.create'
                );

            /*
            |--------------------------------------------------------------------------
            | Criação
            |--------------------------------------------------------------------------
            */

            Route::post(
                '/'.$name,
                [
                    AdminController::class,
                    'store',
                ]
            )
                ->defaults(
                    'module',
                    $name
                )
                ->middleware('throttle:admin-actions')
                ->name(
                    $name.'.store'
                );

            /*
            |--------------------------------------------------------------------------
            | Formulário de edição
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/'.$name.'/{id}/editar',
                [
                    AdminController::class,
                    'edit',
                ]
            )
                ->defaults(
                    'module',
                    $name
                )
                ->whereNumber('id')
                ->name(
                    $name.'.edit'
                );

            /*
            |--------------------------------------------------------------------------
            | Atualização
            |--------------------------------------------------------------------------
            */

            Route::put(
                '/'.$name.'/{id}',
                [
                    AdminController::class,
                    'update',
                ]
            )
                ->defaults(
                    'module',
                    $name
                )
                ->whereNumber('id')
                ->middleware('throttle:admin-actions')
                ->name(
                    $name.'.update'
                );

            /*
            |--------------------------------------------------------------------------
            | Exclusão
            |--------------------------------------------------------------------------
            */

            Route::delete(
                '/'.$name.'/{id}',
                [
                    AdminController::class,
                    'destroy',
                ]
            )
                ->defaults(
                    'module',
                    $name
                )
                ->whereNumber('id')
                ->middleware('throttle:admin-actions')
                ->name(
                    $name.'.destroy'
                );
        }
    });
