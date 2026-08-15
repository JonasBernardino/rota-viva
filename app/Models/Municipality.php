<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'uuid',
    'name',
    'slug',
    'ibge_code',
    'state',
    'schema_name',
    'status',
    'timezone',
    'settings',
])]
class Municipality extends Model
{
    use HasFactory, HasUuids;

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    /**
     * The domains registered for this municipality.
     *
     * @return HasMany<MunicipalityDomain, $this>
     */
    public function domains(): HasMany
    {
        return $this->hasMany(MunicipalityDomain::class);
    }

    /**
     * Check if municipality is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
