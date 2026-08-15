<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMunicipality;
use Illuminate\Database\Eloquent\Model;

class JourneyEvent extends Model
{
    use BelongsToMunicipality;

    protected $fillable = [
        'session_uuid',
        'event_type',
        'payload',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'occurred_at' => 'datetime',
        ];
    }
}
