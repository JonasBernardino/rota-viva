<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMunicipality;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'roteiro_id',
    'evento',
    'titulo',
    'resumo',
    'duracao_total_minutos',
    'custo_total_estimado',
])]
class AdaptacaoRota extends Model
{
    use BelongsToMunicipality, HasFactory;

    protected $table = 'adaptacoes_rota';

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

    public function roteiro(): BelongsTo
    {
        return $this->belongsTo(Roteiro::class, 'roteiro_id');
    }

    public function itinerary(): BelongsTo
    {
        return $this->roteiro();
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemAdaptacaoRota::class, 'adaptacao_rota_id')->orderBy('posicao');
    }

    public function items(): HasMany
    {
        return $this->itens();
    }
}
