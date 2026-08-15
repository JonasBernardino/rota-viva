<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'nome',
    'slug',
    'descricao',
    'nome_local',
    'endereco',
    'latitude',
    'longitude',
    'inicia_em',
    'termina_em',
    'is_gratuito',
    'preco',
    'is_acessivel',
    'categoria',
    'organizador',
    'capacidade',
    'status',
    'imagem_url',
])]
class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'inicia_em' => 'datetime',
            'termina_em' => 'datetime',
            'is_gratuito' => 'boolean',
            'preco' => 'float',
            'is_acessivel' => 'boolean',
            'capacidade' => 'integer',
        ];
    }

    // Accessors de compatibilidade
    public function getNameAttribute(): string
    {
        return $this->attributes['nome'] ?? '';
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->attributes['descricao'] ?? null;
    }

    public function getLocationNameAttribute(): ?string
    {
        return $this->attributes['nome_local'] ?? null;
    }

    public function getStartsAtAttribute(): mixed
    {
        return $this->attributes['inicia_em'] ?? null;
    }

    public function getEndsAtAttribute(): mixed
    {
        return $this->attributes['termina_em'] ?? null;
    }
}
