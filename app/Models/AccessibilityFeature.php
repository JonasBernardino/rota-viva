<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'name',
    'slug',
    'icon',
    'description',
])]
class AccessibilityFeature extends Model
{
    use HasFactory;

    /**
     * Places with this accessibility feature.
     *
     * @return BelongsToMany<Place, $this>
     */
    public function places(): BelongsToMany
    {
        return $this->belongsToMany(
            Place::class,
            'place_accessibility_features'
        );
    }
}
