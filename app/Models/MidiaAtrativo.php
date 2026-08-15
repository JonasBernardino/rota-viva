<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'atrativo_id',
    'tipo',
    'url',
    'titulo',
    'descricao_acessibilidade',
    'autor',
    'is_destaque',
    'ordem',
])]
class MidiaAtrativo extends Model
{
    use HasFactory;

    protected $table = 'midias_atrativos';

    protected function casts(): array
    {
        return [
            'is_destaque' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    public function atrativo(): BelongsTo
    {
        return $this->belongsTo(Atrativo::class, 'atrativo_id');
    }
}
