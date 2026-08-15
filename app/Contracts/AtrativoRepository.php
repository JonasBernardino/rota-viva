<?php

namespace App\Contracts;

use App\Models\Atrativo;
use Illuminate\Database\Eloquent\Collection;

interface AtrativoRepository
{
    /**
     * @return Collection<int, Atrativo>
     */
    public function available(): Collection;
}
