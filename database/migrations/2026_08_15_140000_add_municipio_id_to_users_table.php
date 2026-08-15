<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('municipio_id')
                ->nullable()
                ->after('password')
                ->constrained('municipios')
                ->nullOnDelete();

            $table->index(['municipio_id', 'can_access_admin_panel']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['municipio_id', 'can_access_admin_panel']);
            $table->dropConstrainedForeignId('municipio_id');
        });
    }
};
