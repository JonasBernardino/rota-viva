<?php

namespace App\Repositories;

use App\Contracts\PlaceRepository;
use App\Models\Place;
use Illuminate\Support\Collection;

class EloquentPlaceRepository implements PlaceRepository
{
    public function getAvailablePlaces(): Collection
    {
        return Place::query()
            ->with([
                'category',
                'schedules',
                'accessibilityFeatures',
            ])
            ->where('is_available', true)
            ->get();
    }
}