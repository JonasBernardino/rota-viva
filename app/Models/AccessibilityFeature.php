<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AccessibilityFeature extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function places(): BelongsToMany
    {
        return $this->belongsToMany(
            Place::class,
            'place_accessibility_features'
        );
    }
}