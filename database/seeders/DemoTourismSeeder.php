<?php

namespace Database\Seeders;

use App\Models\AccessibilityFeature;
use App\Models\Category;
use App\Models\Place;
use Illuminate\Database\Seeder;

class DemoTourismSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Categorias
        |--------------------------------------------------------------------------
        */

        $categories = collect([
            'cultura' => 'Cultura e história',
            'natureza' => 'Natureza',
            'gastronomia' => 'Gastronomia',
            'lazer' => 'Lazer',
            'aventura' => 'Aventura',
            'artesanato' => 'Artesanato',
        ])->mapWithKeys(function ($name, $slug) {
            $category = Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name],
            );

            return [$slug => $category];
        });

        /*
        |--------------------------------------------------------------------------
        | Recursos de acessibilidade
        |--------------------------------------------------------------------------
        */

        $accessibilityFeatures = collect([
            'mobility' => 'Mobilidade reduzida',
            'wheelchair' => 'Cadeira de rodas',
            'visual' => 'Recursos para deficiência visual',
        ])->mapWithKeys(function ($name, $slug) {
            $feature = AccessibilityFeature::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name],
            );

            return [$slug => $feature];
        });

        /*
        |--------------------------------------------------------------------------
        | Atrativos
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        |
        | - Locais são reais.
        | - Coordenadas são adequadas para demonstração do mapa.
        | - Preços/durações/horários são dados de DEMONSTRAÇÃO.
        | - Não devem ser tratados como dados oficiais de turismo.
        |
        */

        $places = [

            /*
            |--------------------------------------------------------------------------
            | 1. Estação Cabo Branco
            |--------------------------------------------------------------------------
            |
            | Cultura
            | Coberto
            | Gratuito
            | Diurno
            | Bom para família
            |
            */

            [
                'category' => 'cultura',

                'data' => [
                    'name' => 'Estação Cabo Branco',
                    'slug' => 'estacao-cabo-branco',

                    'description' => 'Espaço dedicado à ciência, cultura e artes, com arquitetura marcante e programação cultural.',

                    'duration_minutes' => 90,

                    'average_cost' => 0,

                    'is_outdoor' => false,

                    'suitable_for_children' => true,

                    'intensity' => 'low',

                    'latitude' => -7.148156,
                    'longitude' => -34.797811,

                    'tags' => [
                        'cultura',
                        'cultural',
                        'historia',
                        'arte',
                        'arquitetura',
                        'familia',
                        'criancas',
                        'tranquilo',
                        'calmo',
                    ],

                    'is_available' => true,
                ],

                'accessibility' => [
                    'mobility',
                    'wheelchair',
                ],

                // Horário controlado para demonstração.
                'schedule' => [
                    0 => ['10:00', '18:00'],
                    1 => ['09:00', '18:00'],
                    2 => ['09:00', '18:00'],
                    3 => ['09:00', '18:00'],
                    4 => ['09:00', '18:00'],
                    5 => ['09:00', '18:00'],
                    6 => ['10:00', '18:00'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 2. Centro Cultural São Francisco
            |--------------------------------------------------------------------------
            |
            | Cultura / patrimônio
            | Coberto
            | Baixo custo
            | Diurno
            |
            */

            [
                'category' => 'cultura',

                'data' => [
                    'name' => 'Centro Cultural São Francisco',
                    'slug' => 'centro-cultural-sao-francisco',

                    'description' => 'Conjunto histórico e cultural no Centro Histórico de João Pessoa.',

                    'duration_minutes' => 75,

                    // VALOR MOCK para permitir teste de baixo custo.
                    'average_cost' => 15,

                    'is_outdoor' => false,

                    'suitable_for_children' => true,

                    'intensity' => 'low',

                    'latitude' => -7.115230,
                    'longitude' => -34.884090,

                    'tags' => [
                        'cultura',
                        'cultural',
                        'historia',
                        'historico',
                        'patrimonio',
                        'religioso',
                        'arte',
                        'arquitetura',
                        'tranquilo',
                    ],

                    'is_available' => true,
                ],

                'accessibility' => [
                    'mobility',
                ],

                'schedule' => [
                    0 => ['09:00', '15:00'],
                    1 => null,
                    2 => ['09:00', '17:00'],
                    3 => ['09:00', '17:00'],
                    4 => ['09:00', '17:00'],
                    5 => ['09:00', '17:00'],
                    6 => ['09:00', '17:00'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 3. Hotel Globo
            |--------------------------------------------------------------------------
            |
            | Cultura
            | Baixo esforço
            | Gratuito
            | Fecha cedo
            |
            */

            [
                'category' => 'cultura',

                'data' => [
                    'name' => 'Hotel Globo',
                    'slug' => 'hotel-globo',

                    'description' => 'Patrimônio histórico no Varadouro, conhecido pela arquitetura e vista para o Rio Sanhauá.',

                    'duration_minutes' => 45,

                    'average_cost' => 0,

                    'is_outdoor' => false,

                    'suitable_for_children' => true,

                    'intensity' => 'low',

                    'latitude' => -7.112360,
                    'longitude' => -34.888870,

                    'tags' => [
                        'cultura',
                        'cultural',
                        'historia',
                        'historico',
                        'patrimonio',
                        'arquitetura',
                        'fotografia',
                        'tranquilo',
                    ],

                    'is_available' => true,
                ],

                'accessibility' => [
                    'mobility',
                ],

                'schedule' => [
                    0 => ['08:00', '17:00'],
                    1 => ['08:00', '17:00'],
                    2 => ['08:00', '17:00'],
                    3 => ['08:00', '17:00'],
                    4 => ['08:00', '17:00'],
                    5 => ['08:00', '17:00'],
                    6 => ['08:00', '17:00'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 4. Farol do Cabo Branco
            |--------------------------------------------------------------------------
            |
            | Outdoor
            | Natureza
            | Gratuito
            | Intensidade média
            |
            */

            [
                'category' => 'natureza',

                'data' => [
                    'name' => 'Farol do Cabo Branco',
                    'slug' => 'farol-do-cabo-branco',

                    'description' => 'Um dos cartões-postais da cidade, localizado na região do Cabo Branco e Ponta do Seixas.',

                    'duration_minutes' => 45,

                    'average_cost' => 0,

                    'is_outdoor' => true,

                    'suitable_for_children' => true,

                    'intensity' => 'medium',

                    'latitude' => -7.148739,
                    'longitude' => -34.796583,

                    'tags' => [
                        'natureza',
                        'paisagem',
                        'fotografia',
                        'mar',
                        'aventura',
                        'aventureiro',
                        'radical',
                        'passeio',
                    ],

                    'is_available' => true,
                ],

                'accessibility' => [
                    'mobility',
                ],

                /*
                 * Mantemos horário amplo para permitir
                 * testes em diferentes momentos do dia.
                 */
                'schedule' => [
                    0 => ['06:00', '21:00'],
                    1 => ['06:00', '21:00'],
                    2 => ['06:00', '21:00'],
                    3 => ['06:00', '21:00'],
                    4 => ['06:00', '21:00'],
                    5 => ['06:00', '21:00'],
                    6 => ['06:00', '21:00'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 5. Mercado de Artesanato Paraibano
            |--------------------------------------------------------------------------
            |
            | Indoor
            | Cultura / artesanato
            | Gratuito para visitar
            | Possibilidade de compras
            |
            */

            [
                'category' => 'artesanato',

                'data' => [
                    'name' => 'Mercado de Artesanato Paraibano',
                    'slug' => 'mercado-de-artesanato-paraibano',

                    'description' => 'Espaço dedicado ao artesanato e à produção cultural paraibana.',

                    'duration_minutes' => 60,

                    /*
                     * Custo estimado de experiência/consumo
                     * para TESTE do orçamento.
                     */
                    'average_cost' => 40,

                    'is_outdoor' => false,

                    'suitable_for_children' => true,

                    'intensity' => 'low',

                    'latitude' => -7.116900,
                    'longitude' => -34.827600,

                    'tags' => [
                        'cultura',
                        'cultural',
                        'artesanato',
                        'compras',
                        'arte',
                        'local',
                        'familia',
                        'tranquilo',
                    ],

                    'is_available' => true,
                ],

                'accessibility' => [
                    'mobility',
                    'wheelchair',
                ],

                'schedule' => [
                    0 => ['09:00', '19:00'],
                    1 => ['09:00', '19:00'],
                    2 => ['09:00', '19:00'],
                    3 => ['09:00', '19:00'],
                    4 => ['09:00', '19:00'],
                    5 => ['09:00', '19:00'],
                    6 => ['09:00', '19:00'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 6. Praia do Jacaré
            |--------------------------------------------------------------------------
            |
            | Cabedelo
            | Outdoor
            | Tarde/noite
            | Pôr do sol
            |
            */

            [
                'category' => 'natureza',

                'data' => [
                    'name' => 'Praia do Jacaré',
                    'slug' => 'praia-do-jacare',

                    'description' => 'Atrativo turístico de Cabedelo conhecido especialmente pela experiência do pôr do sol às margens do Rio Paraíba.',

                    'duration_minutes' => 90,

                    // Estimativa MOCK de consumo.
                    'average_cost' => 50,

                    'is_outdoor' => true,

                    'suitable_for_children' => true,

                    'intensity' => 'low',

                    'latitude' => -7.108333,
                    'longitude' => -34.878889,

                    'tags' => [
                        'natureza',
                        'paisagem',
                        'por-do-sol',
                        'musica',
                        'cultura',
                        'romantico',
                        'familia',
                        'tranquilo',
                        'calmo',
                    ],

                    'is_available' => true,
                ],

                'accessibility' => [
                    'mobility',
                ],

                /*
                 * Intencionalmente disponível
                 * mais tarde para testar rotas noturnas.
                 */
                'schedule' => [
                    0 => ['14:00', '22:00'],
                    1 => ['14:00', '22:00'],
                    2 => ['14:00', '22:00'],
                    3 => ['14:00', '22:00'],
                    4 => ['14:00', '22:00'],
                    5 => ['14:00', '22:00'],
                    6 => ['14:00', '22:00'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 7. Areia Vermelha
            |--------------------------------------------------------------------------
            |
            | Cabedelo
            | Outdoor
            | Aventura
            | Custo alto
            | Horário curto
            |
            */

            [
                'category' => 'aventura',

                'data' => [
                    'name' => 'Parque Estadual Marinho de Areia Vermelha',
                    'slug' => 'areia-vermelha',

                    'description' => 'Área marinha protegida em Cabedelo, conhecida pelo banco de areia e ambiente recifal.',

                    'duration_minutes' => 180,

                    /*
                     * MOCK de transporte/passeio.
                     *
                     * Importante para testar orçamento.
                     */
                    'average_cost' => 180,

                    'is_outdoor' => true,

                    'suitable_for_children' => true,

                    'intensity' => 'high',

                    'latitude' => -7.016000,
                    'longitude' => -34.806000,

                    'tags' => [
                        'natureza',
                        'aventura',
                        'aventureiro',
                        'radical',
                        'mar',
                        'praia',
                        'piscinas-naturais',
                        'ecoturismo',
                    ],

                    'is_available' => true,
                ],

                'accessibility' => [],

                /*
                 * Horário MOCK para representar
                 * uma atração dependente de condição natural.
                 */
                'schedule' => [
                    0 => ['07:00', '14:00'],
                    1 => ['07:00', '14:00'],
                    2 => ['07:00', '14:00'],
                    3 => ['07:00', '14:00'],
                    4 => ['07:00', '14:00'],
                    5 => ['07:00', '14:00'],
                    6 => ['07:00', '14:00'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 8. Piscinas Naturais de Picãozinho
            |--------------------------------------------------------------------------
            |
            | Outdoor
            | Aventura
            | Pago
            | Manhã
            |
            */

            [
                'category' => 'aventura',

                'data' => [
                    'name' => 'Piscinas Naturais de Picãozinho',
                    'slug' => 'piscinas-naturais-picaozinho',

                    'description' => 'Experiência marítima nas piscinas naturais próximas à orla de João Pessoa.',

                    'duration_minutes' => 150,

                    // MOCK para passeio embarcado.
                    'average_cost' => 120,

                    'is_outdoor' => true,

                    'suitable_for_children' => true,

                    'intensity' => 'medium',

                    'latitude' => -7.115000,
                    'longitude' => -34.790000,

                    'tags' => [
                        'natureza',
                        'aventura',
                        'aventureiro',
                        'radical',
                        'mar',
                        'mergulho',
                        'piscinas-naturais',
                        'familia',
                    ],

                    'is_available' => true,
                ],

                'accessibility' => [],

                'schedule' => [
                    0 => ['07:00', '14:00'],
                    1 => ['07:00', '14:00'],
                    2 => ['07:00', '14:00'],
                    3 => ['07:00', '14:00'],
                    4 => ['07:00', '14:00'],
                    5 => ['07:00', '14:00'],
                    6 => ['07:00', '14:00'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 9. Parque Solon de Lucena
            |--------------------------------------------------------------------------
            |
            | Outdoor
            | Gratuito
            | Horário amplo
            |
            */

            [
                'category' => 'lazer',

                'data' => [
                    'name' => 'Parque Solon de Lucena',
                    'slug' => 'parque-solon-de-lucena',

                    'description' => 'Um dos espaços urbanos mais tradicionais da região central de João Pessoa.',

                    'duration_minutes' => 60,

                    'average_cost' => 0,

                    'is_outdoor' => true,

                    'suitable_for_children' => true,

                    'intensity' => 'low',

                    'latitude' => -7.119800,
                    'longitude' => -34.882600,

                    'tags' => [
                        'lazer',
                        'natureza',
                        'cidade',
                        'familia',
                        'criancas',
                        'caminhada',
                        'tranquilo',
                        'gratuito',
                    ],

                    'is_available' => true,
                ],

                'accessibility' => [
                    'mobility',
                    'wheelchair',
                ],

                'schedule' => [
                    0 => ['06:00', '23:00'],
                    1 => ['06:00', '23:00'],
                    2 => ['06:00', '23:00'],
                    3 => ['06:00', '23:00'],
                    4 => ['06:00', '23:00'],
                    5 => ['06:00', '23:00'],
                    6 => ['06:00', '23:00'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 10. Mangai
            |--------------------------------------------------------------------------
            |
            | Gastronomia
            | Indoor
            | Custo mais alto
            | Funciona à noite
            |
            */

            [
                'category' => 'gastronomia',

                'data' => [
                    'name' => 'Mangai',
                    'slug' => 'mangai',

                    'description' => 'Experiência gastronômica voltada à culinária regional nordestina.',

                    'duration_minutes' => 90,

                    /*
                     * MOCK para testar cenário de
                     * orçamento mais elevado.
                     */
                    'average_cost' => 120,

                    'is_outdoor' => false,

                    'suitable_for_children' => true,

                    'intensity' => 'low',

                    'latitude' => -7.103800,
                    'longitude' => -34.834500,

                    'tags' => [
                        'gastronomia',
                        'gastronomico',
                        'comida',
                        'culinaria',
                        'regional',
                        'familia',
                        'cultura',
                        'tranquilo',
                    ],

                    'is_available' => true,
                ],

                'accessibility' => [
                    'mobility',
                    'wheelchair',
                ],

                'schedule' => [
                    0 => ['11:30', '22:00'],
                    1 => ['11:30', '22:00'],
                    2 => ['11:30', '22:00'],
                    3 => ['11:30', '22:00'],
                    4 => ['11:30', '22:00'],
                    5 => ['11:30', '22:00'],
                    6 => ['11:30', '22:00'],
                ],
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Persistência
        |--------------------------------------------------------------------------
        */

        foreach ($places as $placeDefinition) {
            $category = $categories->get(
                $placeDefinition['category']
            );

            $data = $placeDefinition['data'];

            $place = Place::updateOrCreate(
                [
                    'slug' => $data['slug'],
                ],
                [
                    ...$data,
                    'category_id' => $category->id,
                ],
            );

            /*
            |--------------------------------------------------------------------------
            | Acessibilidade
            |--------------------------------------------------------------------------
            */

            $featureIds = collect(
                $placeDefinition['accessibility']
            )
                ->map(
                    fn ($slug) => $accessibilityFeatures
                        ->get($slug)
                        ?->id
                )
                ->filter()
                ->values()
                ->all();

            $place
                ->accessibilityFeatures()
                ->sync($featureIds);

            /*
            |--------------------------------------------------------------------------
            | Horários
            |--------------------------------------------------------------------------
            */

            /*
             * Primeiro limpamos os horários antigos.
             *
             * Isso evita sobrar uma configuração de
             * execuções anteriores do seeder.
             */
            $place
                ->schedules()
                ->delete();

            foreach (
                $placeDefinition['schedule'] as $dayOfWeek => $schedule
            ) {
                /*
                 * null = fechado naquele dia.
                 */
                if ($schedule === null) {
                    continue;
                }

                [$opensAt, $closesAt] = $schedule;

                $place
                    ->schedules()
                    ->create([
                        'day_of_week' => $dayOfWeek,

                        'opens_at' => $opensAt,

                        'closes_at' => $closesAt,
                    ]);
            }
        }
    }
}
