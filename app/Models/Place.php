<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id',
    'name',
    'slug',
    'description',
    'latitude',
    'longitude',
    'is_outdoor',
    'duration_minutes',
    'average_cost',
    'suitable_for_children',
    'intensity',
    'tags',
    'is_available',
])]
class Place extends Model
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
            'latitude' => 'float',
            'longitude' => 'float',
            'is_outdoor' => 'boolean',
            'is_indoor' => 'boolean',
            'duration_minutes' => 'integer',
            'estimated_duration_minutes' => 'integer',
            'average_cost' => 'float',
            'cost' => 'decimal:2',
            'is_free' => 'boolean',
            'suitable_for_children' => 'boolean',
            'suitable_for_kids' => 'boolean',
            'suitable_for_seniors' => 'boolean',
            'suitable_for_rain' => 'boolean',
            'is_accessible' => 'boolean',
            'is_available' => 'boolean',
            'featured' => 'boolean',
            'tags' => 'array',
        ];
    }

    /**
     * Category of this place.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Schedules for this place.
     *
     * @return HasMany<PlaceSchedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(PlaceSchedule::class);
    }

    /**
     * Media assets for this place.
     *
     * @return HasMany<PlaceMedia, $this>
     */
    public function media(): HasMany
    {
        return $this->hasMany(PlaceMedia::class)->orderBy('display_order');
    }

    /**
     * Accessibility features available at this place.
     *
     * @return BelongsToMany<AccessibilityFeature, $this>
     */
    public function accessibilityFeatures(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibilityFeature::class,
            'place_accessibility_features'
        );
    }

    /**
     * Check if place is open during the given timeframe.
     */
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

        if (! $schedule) {
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
