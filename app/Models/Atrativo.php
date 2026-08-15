<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMunicipality;
use App\Services\Tenant\TenantManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'categoria_id',
    'nome',
    'slug',
    'descricao',
    'latitude',
    'longitude',
    'is_ar_livre',
    'duracao_minutos',
    'custo_medio',
    'adequado_criancas',
    'intensidade',
    'tags',
    'is_disponivel',
])]
class Atrativo extends Model
{
    use BelongsToMunicipality, HasFactory, SoftDeletes;

    protected $table = 'atrativos';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_ar_livre' => 'boolean',
            'duracao_minutos' => 'integer',
            'custo_medio' => 'float',
            'adequado_criancas' => 'boolean',
            'tags' => 'array',
            'is_disponivel' => 'boolean',
        ];
    }

    // Accessors para manter compatibilidade com nomes em inglês se necessário
    public function getNameAttribute(): string
    {
        return $this->attributes['nome'] ?? '';
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->attributes['descricao'] ?? null;
    }

    public function getDurationMinutesAttribute(): int
    {
        return (int) ($this->attributes['duracao_minutos'] ?? 0);
    }

    public function getAverageCostAttribute(): float
    {
        return (float) ($this->attributes['custo_medio'] ?? 0);
    }

    public function getIsOutdoorAttribute(): bool
    {
        return (bool) ($this->attributes['is_ar_livre'] ?? false);
    }

    public function getSuitableForChildrenAttribute(): bool
    {
        return (bool) ($this->attributes['adequado_criancas'] ?? true);
    }

    public function getIsAvailableAttribute(): bool
    {
        return (bool) ($this->attributes['is_disponivel'] ?? true);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function category(): BelongsTo
    {
        return $this->categoria();
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(HorarioAtrativo::class, 'atrativo_id');
    }

    public function schedules(): HasMany
    {
        return $this->horarios();
    }

    public function recursosAcessibilidade(): BelongsToMany
    {
        $relation = $this->belongsToMany(
            RecursoAcessibilidade::class,
            'atrativo_recursos_acessibilidade',
            'atrativo_id',
            'recurso_acessibilidade_id'
        );

        $municipality = app(TenantManager::class)->current();

        if ($municipality !== null) {
            $relation
                ->wherePivot('municipio_id', $municipality->id)
                ->withPivotValue('municipio_id', $municipality->id);
        }

        return $relation;
    }

    public function accessibilityFeatures(): BelongsToMany
    {
        return $this->recursosAcessibilidade();
    }

    public function midias(): HasMany
    {
        return $this->hasMany(MidiaAtrativo::class, 'atrativo_id')->orderBy('ordem');
    }

    public function isOpenDuring(Carbon $start, int $durationMinutes): bool
    {
        if ($this->horarios->isEmpty()) {
            return true;
        }

        $dayOfWeek = $start->dayOfWeek;
        $schedule = $this->horarios->firstWhere('dia_semana', $dayOfWeek);

        if (! $schedule) {
            return false;
        }

        $opensAt = Carbon::createFromTimeString($schedule->abre_as);
        $closesAt = Carbon::createFromTimeString($schedule->fecha_as);
        $visitEnd = $start->copy()->addMinutes($durationMinutes);

        return $start->gte($opensAt) && $visitEnd->lte($closesAt);
    }
}
