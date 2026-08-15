<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'usuario_id',
    'acao',
    'entidade_tipo',
    'entidade_id',
    'valores_anteriores',
    'valores_novos',
    'ip_address',
])]
class LogAuditoria extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'logs_auditoria';

    protected function casts(): array
    {
        return [
            'valores_anteriores' => 'array',
            'valores_novos' => 'array',
            'criado_em' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
