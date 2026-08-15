<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'insight_type',
    'title',
    'description',
    'metrics',
    'severity',
    'status',
    'resolved_at',
])]
class DestinationInsight extends Model
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
            'metrics' => 'array',
            'resolved_at' => 'datetime',
        ];
    }
}
