<?php

namespace Database\Seeders;

use App\Models\Atrativo;
use App\Models\Categoria;
use App\Models\RecursoAcessibilidade;
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
            $category = Categoria::updateOrCreate(
                ['slug' => $slug],
                ['nome' => $name],
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
            $feature = RecursoAcessibilidade::updateOrCreate(
                ['slug' => $slug],
                ['nome' => $name],
            );

            return [$slug => $feature];
        });

        /*
        |--------------------------------------------------------------------------
        | Atrativos
        |--------------------------------------------------------------------------
        */

        $places = [
            [
                'category' => 'cultura',
                'data' => [
                    'nome' => 'Estação Cabo Branco',
                    'slug' => 'estacao-cabo-branco',
                    'descricao' => 'Espaço dedicado à ciência, cultura e artes, com arquitetura marcante e programação cultural.',
                    'duracao_minutos' => 90,
                    'custo_medio' => 0,
                    'is_ar_livre' => false,
                    'adequado_criancas' => true,
                    'intensidade' => 'low',
                    'latitude' => -7.148156,
                    'longitude' => -34.797811,
                    'tags' => ['cultura', 'cultural', 'historia', 'arte', 'arquitetura', 'familia', 'criancas', 'tranquilo', 'calmo'],
                    'is_disponivel' => true,
                ],
                'accessibility' => ['mobility', 'wheelchair'],
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
            [
                'category' => 'cultura',
                'data' => [
                    'nome' => 'Centro Cultural São Francisco',
                    'slug' => 'centro-cultural-sao-francisco',
                    'descricao' => 'Conjunto histórico e cultural no Centro Histórico de João Pessoa.',
                    'duracao_minutos' => 75,
                    'custo_medio' => 15,
                    'is_ar_livre' => false,
                    'adequado_criancas' => true,
                    'intensidade' => 'low',
                    'latitude' => -7.115230,
                    'longitude' => -34.884090,
                    'tags' => ['cultura', 'cultural', 'historia', 'historico', 'patrimonio', 'religioso', 'arte', 'arquitetura', 'tranquilo'],
                    'is_disponivel' => true,
                ],
                'accessibility' => ['mobility'],
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
            [
                'category' => 'cultura',
                'data' => [
                    'nome' => 'Hotel Globo',
                    'slug' => 'hotel-globo',
                    'descricao' => 'Patrimônio histórico no Varadouro, conhecido pela arquitetura e vista para o Rio Sanhauá.',
                    'duracao_minutos' => 45,
                    'custo_medio' => 0,
                    'is_ar_livre' => false,
                    'adequado_criancas' => true,
                    'intensidade' => 'low',
                    'latitude' => -7.112360,
                    'longitude' => -34.888870,
                    'tags' => ['cultura', 'cultural', 'historia', 'historico', 'patrimonio', 'arquitetura', 'fotografia', 'tranquilo'],
                    'is_disponivel' => true,
                ],
                'accessibility' => ['mobility'],
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
            [
                'category' => 'natureza',
                'data' => [
                    'nome' => 'Farol do Cabo Branco',
                    'slug' => 'farol-do-cabo-branco',
                    'descricao' => 'Um dos cartões-postais da cidade, localizado na região do Cabo Branco e Ponta do Seixas.',
                    'duracao_minutos' => 45,
                    'custo_medio' => 0,
                    'is_ar_livre' => true,
                    'adequado_criancas' => true,
                    'intensidade' => 'medium',
                    'latitude' => -7.148739,
                    'longitude' => -34.796583,
                    'tags' => ['natureza', 'paisagem', 'fotografia', 'mar', 'aventura', 'aventureiro', 'radical', 'passeio'],
                    'is_disponivel' => true,
                ],
                'accessibility' => ['mobility'],
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
            [
                'category' => 'artesanato',
                'data' => [
                    'nome' => 'Mercado de Artesanato Paraibano',
                    'slug' => 'mercado-de-artesanato-paraibano',
                    'descricao' => 'Espaço dedicado ao artesanato e à produção cultural paraibana.',
                    'duracao_minutos' => 60,
                    'custo_medio' => 40,
                    'is_ar_livre' => false,
                    'adequado_criancas' => true,
                    'intensidade' => 'low',
                    'latitude' => -7.116900,
                    'longitude' => -34.827600,
                    'tags' => ['cultura', 'cultural', 'artesanato', 'compras', 'arte', 'local', 'familia', 'tranquilo'],
                    'is_disponivel' => true,
                ],
                'accessibility' => ['mobility', 'wheelchair'],
                'schedule' => [
                    0 => ['00:00', '23:59'],
                    1 => ['00:00', '23:59'],
                    2 => ['00:00', '23:59'],
                    3 => ['00:00', '23:59'],
                    4 => ['00:00', '23:59'],
                    5 => ['00:00', '23:59'],
                    6 => ['00:00', '23:59'],
                ],
            ],
            [
                'category' => 'natureza',
                'data' => [
                    'nome' => 'Praia do Jacaré',
                    'slug' => 'praia-do-jacare',
                    'descricao' => 'Atrativo turístico de Cabedelo conhecido especialmente pela experiência do pôr do sol às margens do Rio Paraíba.',
                    'duracao_minutos' => 90,
                    'custo_medio' => 50,
                    'is_ar_livre' => true,
                    'adequado_criancas' => true,
                    'intensidade' => 'low',
                    'latitude' => -7.108333,
                    'longitude' => -34.878889,
                    'tags' => ['natureza', 'paisagem', 'por-do-sol', 'musica', 'cultura', 'romantico', 'familia', 'tranquilo', 'calmo'],
                    'is_disponivel' => true,
                ],
                'accessibility' => ['mobility'],
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
            [
                'category' => 'aventura',
                'data' => [
                    'nome' => 'Parque Estadual Marinho de Areia Vermelha',
                    'slug' => 'areia-vermelha',
                    'descricao' => 'Área marinha protegida em Cabedelo, conhecida pelo banco de areia e ambiente recifal.',
                    'duracao_minutos' => 180,
                    'custo_medio' => 180,
                    'is_ar_livre' => true,
                    'adequado_criancas' => true,
                    'intensidade' => 'high',
                    'latitude' => -7.016000,
                    'longitude' => -34.806000,
                    'tags' => ['natureza', 'aventura', 'aventureiro', 'radical', 'mar', 'praia', 'piscinas-naturais', 'ecoturismo'],
                    'is_disponivel' => true,
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
            [
                'category' => 'aventura',
                'data' => [
                    'nome' => 'Piscinas Naturais de Picãozinho',
                    'slug' => 'piscinas-naturais-picaozinho',
                    'descricao' => 'Experiência marítima nas piscinas naturais próximas à orla de João Pessoa.',
                    'duracao_minutos' => 150,
                    'custo_medio' => 120,
                    'is_ar_livre' => true,
                    'adequado_criancas' => true,
                    'intensidade' => 'medium',
                    'latitude' => -7.115000,
                    'longitude' => -34.790000,
                    'tags' => ['natureza', 'aventura', 'aventureiro', 'radical', 'mar', 'mergulho', 'piscinas-naturais', 'familia'],
                    'is_disponivel' => true,
                ],
                'accessibility' => [],
                'schedule' => [
                    0 => ['00:00', '23:59'],
                    1 => ['00:00', '23:59'],
                    2 => ['00:00', '23:59'],
                    3 => ['00:00', '23:59'],
                    4 => ['00:00', '23:59'],
                    5 => ['00:00', '23:59'],
                    6 => ['00:00', '23:59'],
                ],
            ],
            [
                'category' => 'lazer',
                'data' => [
                    'nome' => 'Parque Solon de Lucena',
                    'slug' => 'parque-solon-de-lucena',
                    'descricao' => 'Um dos espaços urbanos mais tradicionais da região central de João Pessoa.',
                    'duracao_minutos' => 60,
                    'custo_medio' => 0,
                    'is_ar_livre' => true,
                    'adequado_criancas' => true,
                    'intensidade' => 'low',
                    'latitude' => -7.119800,
                    'longitude' => -34.882600,
                    'tags' => ['lazer', 'natureza', 'cidade', 'familia', 'criancas', 'caminhada', 'tranquilo', 'gratuito'],
                    'is_disponivel' => true,
                ],
                'accessibility' => ['mobility', 'wheelchair'],
                'schedule' => [
                    0 => ['00:00', '23:59'],
                    1 => ['00:00', '23:59'],
                    2 => ['00:00', '23:59'],
                    3 => ['00:00', '23:59'],
                    4 => ['00:00', '23:59'],
                    5 => ['00:00', '23:59'],
                    6 => ['00:00', '23:59'],
                ],
            ],
            [
                'category' => 'gastronomia',
                'data' => [
                    'nome' => 'Mangai',
                    'slug' => 'mangai',
                    'descricao' => 'Experiência gastronômica voltada à culinária regional nordestina.',
                    'duracao_minutos' => 90,
                    'custo_medio' => 120,
                    'is_ar_livre' => false,
                    'adequado_criancas' => true,
                    'intensidade' => 'low',
                    'latitude' => -7.103800,
                    'longitude' => -34.834500,
                    'tags' => ['gastronomia', 'gastronomico', 'comida', 'culinaria', 'regional', 'familia', 'cultura', 'tranquilo'],
                    'is_disponivel' => true,
                ],
                'accessibility' => ['mobility', 'wheelchair'],
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

            /** @var Atrativo $place */
            $place = Atrativo::updateOrCreate(
                [
                    'slug' => $data['slug'],
                ],
                [
                    ...$data,
                    'categoria_id' => $category->id,
                ],
            );

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
                ->recursosAcessibilidade()
                ->sync($featureIds);

            $place
                ->horarios()
                ->delete();

            foreach (
                $placeDefinition['schedule'] as $dayOfWeek => $schedule
            ) {
                if ($schedule === null) {
                    continue;
                }

                [$opensAt, $closesAt] = $schedule;

                $place
                    ->horarios()
                    ->create([
                        'dia_semana' => $dayOfWeek,
                        'abre_as' => $opensAt,
                        'fecha_as' => $closesAt,
                    ]);
            }
        }
    }
}
