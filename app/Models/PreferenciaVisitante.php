<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'descricao_original',
    'humores',
    'interesses',
    'minutos_disponiveis',
    'orcamento',
    'tem_criancas',
    'transporte',
    'requisitos_acessibilidade',
    'intensidade',
])]
class PreferenciaVisitante extends Model
{
    use HasFactory;

    protected $table = 'preferencias_visitantes';

    protected function casts(): array
    {
        return [
            'humores' => 'array',
            'interesses' => 'array',
            'minutos_disponiveis' => 'integer',
            'orcamento' => 'float',
            'tem_criancas' => 'boolean',
            'requisitos_acessibilidade' => 'array',
        ];
    }

    public function roteiros(): HasMany
    {
        return $this->hasMany(Roteiro::class, 'preferencia_visitante_id');
    }
}
