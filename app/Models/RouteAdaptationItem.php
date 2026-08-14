<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteAdaptationItem extends Model
{
    protected $fillable = [
        'route_adaptation_id',
        'place_id',
        'position',
        'action',
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

    public function adaptation(): BelongsTo
    {
        return $this->belongsTo(RouteAdaptation::class);
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}