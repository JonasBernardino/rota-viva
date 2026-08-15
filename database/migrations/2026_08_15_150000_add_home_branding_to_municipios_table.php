<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('municipios', function (Blueprint $table): void {
            if (! Schema::hasColumn('municipios', 'brand_name')) {
                $table->string('brand_name')->default('ROTA VIVA')->after('configuracoes');
            }

            if (! Schema::hasColumn('municipios', 'brand_logo_path')) {
                $table->string('brand_logo_path')->nullable()->after('brand_name');
            }

            if (! Schema::hasColumn('municipios', 'hero_eyebrow')) {
                $table->string('hero_eyebrow')->default('Turismo inteligente')->after('brand_logo_path');
            }

            if (! Schema::hasColumn('municipios', 'hero_title')) {
                $table->string('hero_title')->default('Como você quer viver a cidade hoje?')->after('hero_eyebrow');
            }

            if (! Schema::hasColumn('municipios', 'hero_description')) {
                $table->text('hero_description')->nullable()->after('hero_title');
            }

            if (! Schema::hasColumn('municipios', 'hero_image_path')) {
                $table->string('hero_image_path')->nullable()->after('hero_description');
            }

            if (! Schema::hasColumn('municipios', 'hero_image_alt')) {
                $table->string('hero_image_alt')->nullable()->after('hero_image_path');
            }

            if (! Schema::hasColumn('municipios', 'hero_search_placeholder')) {
                $table->string('hero_search_placeholder')->nullable()->after('hero_image_alt');
            }

            if (! Schema::hasColumn('municipios', 'hero_card_title')) {
                $table->string('hero_card_title')->nullable()->after('hero_search_placeholder');
            }

            if (! Schema::hasColumn('municipios', 'hero_card_tags')) {
                $table->json('hero_card_tags')->nullable()->after('hero_card_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('municipios', function (Blueprint $table): void {
            foreach ([
                'hero_card_tags',
                'hero_card_title',
                'hero_search_placeholder',
                'hero_image_alt',
                'hero_image_path',
                'hero_description',
                'hero_title',
                'hero_eyebrow',
                'brand_logo_path',
                'brand_name',
            ] as $column) {
                if (Schema::hasColumn('municipios', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
