<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'original_description',
    'moods',
    'interests',
    'available_minutes',
    'budget',
    'has_children',
    'transport',
    'accessibility_requirements',
    'intensity',
    'session_id',
    'ip_hash',
    'vibe',
    'available_time_minutes',
    'companions',
    'transport_mode',
    'accessibility_required',
    'pace',
    'raw_query',
])]
class VisitorPreference extends Model
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
            'moods' => 'array',
            'interests' => 'array',
            'accessibility_requirements' => 'array',
            'has_children' => 'boolean',
            'accessibility_required' => 'boolean',
            'budget' => 'float',
            'available_minutes' => 'integer',
            'available_time_minutes' => 'integer',
        ];
    }

    /**
     * Itineraries generated for these preferences.
     *
     * @return HasMany<Itinerary, $this>
     */
    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class);
    }
}
