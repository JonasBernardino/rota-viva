<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'slug',
    'business_type',
    'description',
    'address',
    'neighborhood',
    'latitude',
    'longitude',
    'phone',
    'whatsapp',
    'instagram',
    'website',
    'price_range',
    'has_seal_of_quality',
    'validation_status',
    'validation_notes',
    'validated_at',
    'cover_image',
])]
class Business extends Model
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
            'has_seal_of_quality' => 'boolean',
            'validated_at' => 'datetime',
        ];
    }
}
