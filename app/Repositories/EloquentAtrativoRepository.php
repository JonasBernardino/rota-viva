<?php

namespace App\Repositories;

use App\Contracts\AtrativoRepository;
use App\Models\Atrativo;
use Illuminate\Database\Eloquent\Collection;

class EloquentAtrativoRepository implements AtrativoRepository
{
    public function available(): Collection
    {
        return Atrativo::query()
            ->with(['categoria', 'horarios', 'recursosAcessibilidade'])
            ->where('is_disponivel', true)
            ->get();
    }
}
