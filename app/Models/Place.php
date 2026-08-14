<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'duration_minutes',
        'average_cost',
        'is_outdoor',
        'suitable_for_children',
        'intensity',
        'latitude',
        'longitude',
        'tags',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_outdoor' => 'boolean',
            'suitable_for_children' => 'boolean',
            'is_available' => 'boolean',
            'average_cost' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(PlaceSchedule::class);
    }

    public function accessibilityFeatures(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibilityFeature::class,
            'place_accessibility_features'
        );
    }

    public function isOpenDuring(
        Carbon $start,
        int $durationMinutes
    ): bool {
        if ($this->schedules->isEmpty()) {
            return true;
        }

        $end = $start->copy()->addMinutes($durationMinutes);

        $schedule = $this->schedules->firstWhere(
            'day_of_week',
            $start->dayOfWeek
        );

        if (!$schedule) {
            return false;
        }

        $opening = $start->copy()->setTimeFromTimeString(
            $schedule->opens_at
        );

        $closing = $start->copy()->setTimeFromTimeString(
            $schedule->closes_at
        );

        return $start->greaterThanOrEqualTo($opening)
            && $end->lessThanOrEqualTo($closing);
    }
}