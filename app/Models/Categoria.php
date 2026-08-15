<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nome',
    'slug',
])]
class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    public function atrativos(): HasMany
    {
        return $this->hasMany(Atrativo::class, 'categoria_id');
    }
}
