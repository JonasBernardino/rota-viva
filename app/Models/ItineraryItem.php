<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItineraryItem extends Model
{
    protected $fillable = [
        'itinerary_id',
        'place_id',
        'position',
        'duration_minutes',
        'estimated_cost',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'estimated_cost' => 'float',
        ];
    }

    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}