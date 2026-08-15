<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMunicipality;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'atrativo_id',
    'dia_semana',
    'abre_as',
    'fecha_as',
])]
class HorarioAtrativo extends Model
{
    use BelongsToMunicipality, HasFactory;

    protected $table = 'horarios_atrativos';

    protected function casts(): array
    {
        return [
            'dia_semana' => 'integer',
        ];
    }

    public function atrativo(): BelongsTo
    {
        return $this->belongsTo(Atrativo::class, 'atrativo_id');
    }
}
