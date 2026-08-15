<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'itinerary_id',
    'event',
    'trigger_event',
    'title',
    'summary',
    'reason_text',
    'explanation',
    'total_duration_minutes',
    'total_estimated_cost',
    'previous_duration_minutes',
    'new_duration_minutes',
    'previous_cost',
    'new_cost',
])]
class RouteAdaptation extends Model
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
            'total_duration_minutes' => 'integer',
            'total_estimated_cost' => 'float',
            'previous_duration_minutes' => 'integer',
            'new_duration_minutes' => 'integer',
            'previous_cost' => 'float',
            'new_cost' => 'float',
        ];
    }

    /**
     * Itinerary adapted.
     *
     * @return BelongsTo<Itinerary, $this>
     */
    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    /**
     * Items in this adaptation.
     *
     * @return HasMany<RouteAdaptationItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(RouteAdaptationItem::class);
    }
}
