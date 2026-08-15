<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'municipio_id',
    'dominio',
    'is_principal',
    'verificado_em',
])]
class DominioMunicipio extends Model
{
    use HasFactory;

    protected $table = 'dominios_municipios';

    protected function casts(): array
    {
        return [
            'is_principal' => 'boolean',
            'verificado_em' => 'datetime',
        ];
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }
}
