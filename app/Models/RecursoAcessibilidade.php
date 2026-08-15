<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'nome',
    'slug',
])]
class RecursoAcessibilidade extends Model
{
    use HasFactory;

    protected $table = 'recursos_acessibilidade';

    public function atrativos(): BelongsToMany
    {
        return $this->belongsToMany(
            Atrativo::class,
            'atrativo_recursos_acessibilidade',
            'recurso_acessibilidade_id',
            'atrativo_id'
        );
    }
}
