<?php

namespace App\Models\Concerns;

use App\Models\Municipio;
use App\Services\Tenant\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToMunicipality
{
    protected static function bootBelongsToMunicipality(): void
    {
        static::addGlobalScope('municipality', function (Builder $builder): void {
            $municipality = app(TenantManager::class)->current();

            if ($municipality !== null) {
                $builder->where($builder->getModel()->getTable().'.municipio_id', $municipality->id);
            }
        });

        static::saving(function ($model): void {
            $municipality = app(TenantManager::class)->current();

            if ($municipality !== null && empty($model->municipio_id)) {
                $model->forceFill(['municipio_id' => $municipality->id]);
            }
        });
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }
}
