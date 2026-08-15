<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'roteiro_id',
    'atrativo_id',
    'posicao',
    'duracao_minutos',
    'custo_estimado',
    'motivo',
])]
class ItemRoteiro extends Model
{
    use HasFactory;

    protected $table = 'itens_roteiro';

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

    public function roteiro(): BelongsTo
    {
        return $this->belongsTo(Roteiro::class, 'roteiro_id');
    }

    public function itinerary(): BelongsTo
    {
        return $this->roteiro();
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
