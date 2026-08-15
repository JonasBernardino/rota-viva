<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'adaptacao_rota_id',
    'atrativo_id',
    'posicao',
    'acao',
    'duracao_minutos',
    'custo_estimado',
    'motivo',
])]
class ItemAdaptacaoRota extends Model
{
    use HasFactory;

    protected $table = 'itens_adaptacao_rota';

    protected function casts(): array
    {
        return [
            'posicao' => 'integer',
            'duracao_minutos' => 'integer',
            'custo_estimado' => 'float',
        ];
    }

    // Accessors de compatibilidade
    public function getPlaceIdAttribute(): int
    {
        return (int) ($this->attributes['atrativo_id'] ?? 0);
    }

    public function getPositionAttribute(): int
    {
        return (int) ($this->attributes['posicao'] ?? 0);
    }

    public function getActionAttribute(): string
    {
        return $this->attributes['acao'] ?? '';
    }

    public function getDurationMinutesAttribute(): int
    {
        return (int) ($this->attributes['duracao_minutos'] ?? 0);
    }

    public function getEstimatedCostAttribute(): float
    {
        return (float) ($this->attributes['custo_estimado'] ?? 0);
    }

    public function getReasonAttribute(): string
    {
        return $this->attributes['motivo'] ?? '';
    }

    public function adaptacaoRota(): BelongsTo
    {
        return $this->belongsTo(AdaptacaoRota::class, 'adaptacao_rota_id');
    }

    public function adaptation(): BelongsTo
    {
        return $this->adaptacaoRota();
    }

    public function atrativo(): BelongsTo
    {
        return $this->belongsTo(Atrativo::class, 'atrativo_id');
    }

    public function place(): BelongsTo
    {
        return $this->atrativo();
    }
}
