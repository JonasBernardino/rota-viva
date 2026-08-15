<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('municipios') || ! Schema::hasTable('dominios_municipios')) {
            return;
        }

        $municipalities = DB::table('municipios')
            ->select(['id', 'slug'])
            ->whereNotNull('slug')
            ->get();

        foreach ($municipalities as $municipality) {
            $domain = 'rota-viva.'.$municipality->slug.'.test';

            DB::table('dominios_municipios')
                ->where('municipio_id', $municipality->id)
                ->update(['is_principal' => false]);

            DB::table('dominios_municipios')->updateOrInsert(
                ['dominio' => $domain],
                [
                    'municipio_id' => $municipality->id,
                    'is_principal' => true,
                    'verificado_em' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('dominios_municipios')) {
            return;
        }

        DB::table('dominios_municipios')
            ->where('dominio', 'like', 'rota-viva.%.test')
            ->delete();
    }
};
