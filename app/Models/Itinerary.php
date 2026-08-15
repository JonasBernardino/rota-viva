<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'visitor_preference_id',
    'title',
    'summary',
    'total_duration_minutes',
    'total_estimated_cost',
    'status',
])]
class Itinerary extends Model
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
            'is_official' => 'boolean',
        ];
    }

    /**
     * Preference associated with this itinerary.
     *
     * @return BelongsTo<VisitorPreference, $this>
     */
    public function preference(): BelongsTo
    {
        return $this->belongsTo(
            VisitorPreference::class,
            'visitor_preference_id'
        );
    }

    /**
     * Visitor preference associated (alias).
     *
     * @return BelongsTo<VisitorPreference, $this>
     */
    public function visitorPreference(): BelongsTo
    {
        return $this->belongsTo(VisitorPreference::class, 'visitor_preference_id');
    }

    /**
     * Ordered items / stops in this itinerary.
     *
     * @return HasMany<ItineraryItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ItineraryItem::class)
            ->orderBy('position');
    }

    /**
     * Route adaptations applied to this itinerary.
     *
     * @return HasMany<RouteAdaptation, $this>
     */
    public function adaptations(): HasMany
    {
        return $this->hasMany(RouteAdaptation::class)->latest();
    }
}
