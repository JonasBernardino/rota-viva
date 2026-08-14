<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Itinerary extends Model
{
    protected $fillable = [
        'visitor_preference_id',
        'title',
        'summary',
        'total_duration_minutes',
        'total_estimated_cost',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_estimated_cost' => 'float',
        ];
    }

    public function preference(): BelongsTo
    {
        return $this->belongsTo(
            VisitorPreference::class,
            'visitor_preference_id'
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItineraryItem::class)
            ->orderBy('position');
    }
}
