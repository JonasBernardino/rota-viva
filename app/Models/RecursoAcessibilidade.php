<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMunicipality;
use App\Services\Tenant\TenantManager;
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
    use BelongsToMunicipality, HasFactory;

    protected $table = 'recursos_acessibilidade';

    public function atrativos(): BelongsToMany
    {
        $relation = $this->belongsToMany(
            Atrativo::class,
            'atrativo_recursos_acessibilidade',
            'recurso_acessibilidade_id',
            'atrativo_id'
        );

        $municipality = app(TenantManager::class)->current();

        if ($municipality !== null) {
            $relation
                ->wherePivot('municipio_id', $municipality->id)
                ->withPivotValue('municipio_id', $municipality->id);
        }

        return $relation;
    }
}
