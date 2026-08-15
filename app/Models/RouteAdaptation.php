<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteAdaptation extends Model
{
    protected $fillable = [
        'itinerary_id',
        'event',
        'title',
        'summary',
        'total_duration_minutes',
        'total_estimated_cost',
    ];

    protected function casts(): array
    {
        return [
            'total_estimated_cost' => 'float',
        ];
    }

    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RouteAdaptationItem::class)
            ->orderBy('position');
    }
}