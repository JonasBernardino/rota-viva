<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'data_referencia',
    'metrica',
    'valor',
    'dimensoes',
])]
class InsightDestino extends Model
{
    use HasFactory;

    protected $table = 'insights_destino';

    protected function casts(): array
    {
        return [
            'data_referencia' => 'date',
            'valor' => 'float',
            'dimensoes' => 'array',
        ];
    }
}
