<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('municipios', function (Blueprint $table): void {
            if (! Schema::hasColumn('municipios', 'local_economy_eyebrow')) {
                $table->string('local_economy_eyebrow')->default('Economia local')->after('hero_card_tags');
            }

            if (! Schema::hasColumn('municipios', 'local_economy_title')) {
                $table->string('local_economy_title')->default('Cada rota também movimenta o território')->after('local_economy_eyebrow');
            }

            if (! Schema::hasColumn('municipios', 'local_economy_description')) {
                $table->text('local_economy_description')->nullable()->after('local_economy_title');
            }

            if (! Schema::hasColumn('municipios', 'local_economy_stat')) {
                $table->string('local_economy_stat')->nullable()->after('local_economy_description');
            }

            if (! Schema::hasColumn('municipios', 'local_economy_link_label')) {
                $table->string('local_economy_link_label')->nullable()->after('local_economy_stat');
            }

            if (! Schema::hasColumn('municipios', 'local_economy_link_url')) {
                $table->string('local_economy_link_url')->nullable()->after('local_economy_link_label');
            }

            if (! Schema::hasColumn('municipios', 'local_economy_image_path')) {
                $table->string('local_economy_image_path')->nullable()->after('local_economy_link_url');
            }

            if (! Schema::hasColumn('municipios', 'local_economy_image_alt')) {
                $table->string('local_economy_image_alt')->nullable()->after('local_economy_image_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('municipios', function (Blueprint $table): void {
            foreach ([
                'local_economy_image_alt',
                'local_economy_image_path',
                'local_economy_link_url',
                'local_economy_link_label',
                'local_economy_stat',
                'local_economy_description',
                'local_economy_title',
                'local_economy_eyebrow',
            ] as $column) {
                if (Schema::hasColumn('municipios', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
