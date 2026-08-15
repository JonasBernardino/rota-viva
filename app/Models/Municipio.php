<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'uuid',
    'nome',
    'slug',
    'codigo_ibge',
    'uf',
    'nome_schema',
    'status',
    'fuso_horario',
    'configuracoes',
])]
class Municipio extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'municipios';

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'configuracoes' => 'array',
        ];
    }

    public function dominios(): HasMany
    {
        return $this->hasMany(DominioMunicipio::class, 'municipio_id');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(UsuarioPlataforma::class, 'municipio_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
