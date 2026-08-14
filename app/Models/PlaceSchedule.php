<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaceSchedule extends Model
{
    protected $fillable = [
        'place_id',
        'day_of_week',
        'opens_at',
        'closes_at',
    ];

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}