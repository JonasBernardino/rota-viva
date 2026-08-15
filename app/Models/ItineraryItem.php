<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'itinerary_id',
    'place_id',
    'position',
    'stop_order',
    'arrival_time',
    'duration_minutes',
    'estimated_duration_minutes',
    'estimated_cost',
    'reason',
    'reason_for_recommendation',
    'is_indoor',
    'status',
])]
class ItineraryItem extends Model
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
            'stop_order' => 'integer',
            'duration_minutes' => 'integer',
            'estimated_duration_minutes' => 'integer',
            'estimated_cost' => 'float',
            'is_indoor' => 'boolean',
        ];
    }

    /**
     * Itinerary this stop belongs to.
     *
     * @return BelongsTo<Itinerary, $this>
     */
    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    /**
     * Place for this stop.
     *
     * @return BelongsTo<Place, $this>
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}
