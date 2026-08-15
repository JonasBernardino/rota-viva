<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'sessao_id',
    'tipo_interacao',
    'entidade_tipo',
    'entidade_id',
    'metadados',
    'ip_anonimizado',
    'user_agent',
])]
class Interacao extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'interacoes';

    protected function casts(): array
    {
        return [
            'metadados' => 'array',
            'criado_em' => 'datetime',
        ];
    }
}
