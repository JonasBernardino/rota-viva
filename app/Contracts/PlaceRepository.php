<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface PlaceRepository
{
    public function getAvailablePlaces(): Collection;
}
