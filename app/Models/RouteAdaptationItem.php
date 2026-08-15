<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'route_adaptation_id',
    'place_id',
    'removed_place_id',
    'added_place_id',
    'position',
    'action',
    'duration_minutes',
    'estimated_cost',
    'reason',
    'change_reason',
])]
class RouteAdaptationItem extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'duration_minutes' => 'integer',
            'estimated_cost' => 'float',
        ];
    }

    /**
     * Adaptation this item belongs to.
     *
     * @return BelongsTo<RouteAdaptation, $this>
     */
    public function adaptation(): BelongsTo
    {
        return $this->belongsTo(RouteAdaptation::class, 'route_adaptation_id');
    }

    /**
     * Place this item refers to.
     *
     * @return BelongsTo<Place, $this>
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * The place that was removed / substituted.
     *
     * @return BelongsTo<Place, $this>
     */
    public function removedPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'removed_place_id');
    }

    /**
     * The place that was added in substitution.
     *
     * @return BelongsTo<Place, $this>
     */
    public function addedPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'added_place_id');
    }
}
