<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'preferencia_visitante_id',
    'titulo',
    'resumo',
    'duracao_total_minutos',
    'custo_total_estimado',
    'status',
])]
class Roteiro extends Model
{
    use HasFactory;

    protected $table = 'roteiros';

    protected function casts(): array
    {
        return [
            'duracao_total_minutos' => 'integer',
            'custo_total_estimado' => 'float',
        ];
    }

    // Accessors de compatibilidade
    public function getTitleAttribute(): string
    {
        return $this->attributes['titulo'] ?? '';
    }

    public function getSummaryAttribute(): string
    {
        return $this->attributes['resumo'] ?? '';
    }

    public function getTotalDurationMinutesAttribute(): int
    {
        return (int) ($this->attributes['duracao_total_minutos'] ?? 0);
    }

    public function getTotalEstimatedCostAttribute(): float
    {
        return (float) ($this->attributes['custo_total_estimado'] ?? 0);
    }

    public function preferencia(): BelongsTo
    {
        return $this->belongsTo(PreferenciaVisitante::class, 'preferencia_visitante_id');
    }

    public function preference(): BelongsTo
    {
        return $this->preferencia();
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemRoteiro::class, 'roteiro_id')->orderBy('posicao');
    }

    public function items(): HasMany
    {
        return $this->itens();
    }

    public function adaptacoes(): HasMany
    {
        return $this->hasMany(AdaptacaoRota::class, 'roteiro_id')->latest();
    }

    public function adaptations(): HasMany
    {
        return $this->adaptacoes();
    }
}
