<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorPreference extends Model
{
    protected $fillable = [
        'original_description',
        'moods',
        'interests',
        'available_minutes',
        'budget',
        'has_children',
        'transport',
        'accessibility_requirements',
        'intensity',
    ];

    protected function casts(): array
    {
        return [
            'moods' => 'array',
            'interests' => 'array',
            'accessibility_requirements' => 'array',
            'has_children' => 'boolean',
            'budget' => 'float',
        ];
    }
}