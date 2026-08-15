<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'slug',
    'description',
    'location_name',
    'address',
    'latitude',
    'longitude',
    'starts_at',
    'ends_at',
    'is_free',
    'price',
    'is_accessible',
    'category',
    'organizer',
    'capacity',
    'status',
    'image_url',
])]
class Event extends Model
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
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_free' => 'boolean',
            'price' => 'decimal:2',
            'is_accessible' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
            'capacity' => 'integer',
        ];
    }
}
