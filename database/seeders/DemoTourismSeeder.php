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
        $culture = Category::firstOrCreate(
            ['slug' => 'cultura'],
            ['name' => 'Cultura']
        );

        $gastronomy = Category::firstOrCreate(
            ['slug' => 'gastronomia'],
            ['name' => 'Gastronomia']
        );

        $nature = Category::firstOrCreate(
            ['slug' => 'natureza'],
            ['name' => 'Natureza']
        );

        $mobility = AccessibilityFeature::firstOrCreate(
            ['slug' => 'mobility'],
            ['name' => 'Mobilidade reduzida']
        );

        $wheelchair = AccessibilityFeature::firstOrCreate(
            ['slug' => 'wheelchair'],
            ['name' => 'Cadeira de rodas']
        );

        $places = [
            [
                'category' => $culture,

                'data' => [
                    'name' =>
                        'Centro de Cultura e Memória',

                    'slug' =>
                        'centro-de-cultura-e-memoria',

                    'description' =>
                        'Espaço dedicado à história e à memória local.',

                    'duration_minutes' => 60,
                    'average_cost' => 0,
                    'is_outdoor' => false,
                    'suitable_for_children' => true,
                    'intensity' => 'low',

                    'latitude' => -6.9000,
                    'longitude' => -34.8700,

                    'tags' => [
                        'cultura',
                        'historia',
                        'tranquilo',
                        'familia',
                    ],

                    'is_available' => true,
                ],
            ],

            [
                'category' => $gastronomy,

                'data' => [
                    'name' =>
                        'Mercado de Sabores Locais',

                    'slug' =>
                        'mercado-de-sabores-locais',

                    'description' =>
                        'Gastronomia e produtos produzidos no território.',

                    'duration_minutes' => 90,
                    'average_cost' => 35,
                    'is_outdoor' => false,
                    'suitable_for_children' => true,
                    'intensity' => 'low',

                    'latitude' => -6.9050,
                    'longitude' => -34.8750,

                    'tags' => [
                        'gastronomia',
                        'cultura',
                        'familia',
                        'tranquilo',
                    ],

                    'is_available' => true,
                ],
            ],

            [
                'category' => $nature,

                'data' => [
                    'name' =>
                        'Mirante do Encontro',

                    'slug' =>
                        'mirante-do-encontro',

                    'description' =>
                        'Vista panorâmica da paisagem do município.',

                    'duration_minutes' => 45,
                    'average_cost' => 0,
                    'is_outdoor' => true,
                    'suitable_for_children' => true,
                    'intensity' => 'medium',

                    'latitude' => -6.9100,
                    'longitude' => -34.8800,

                    'tags' => [
                        'natureza',
                        'paisagem',
                        'aventura',
                    ],

                    'is_available' => true,
                ],
            ],
        ];

        foreach ($places as $placeData) {
            $category = $placeData['category'];

            $data = $placeData['data'];

            $place = Place::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    ...$data,
                    'category_id' => $category->id,
                ]
            );

            $place
                ->accessibilityFeatures()
                ->syncWithoutDetaching([
                    $mobility->id,
                    $wheelchair->id,
                ]);

            foreach (range(0, 6) as $day) {
                $place->schedules()->updateOrCreate(
                    [
                        'day_of_week' => $day,
                    ],
                    [
                        'opens_at' => '08:00',
                        'closes_at' => '22:00',
                    ]
                );
            }
        }
    }
}