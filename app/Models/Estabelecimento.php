<?php

namespace App\Models;

use App\Models\Concerns\BelongsToMunicipality;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'nome',
    'slug',
    'tipo_estabelecimento',
    'descricao',
    'endereco',
    'bairro',
    'latitude',
    'longitude',
    'telefone',
    'whatsapp',
    'instagram',
    'website',
    'faixa_preco',
    'tem_selo_qualidade',
    'status_validacao',
    'notas_validacao',
    'validado_em',
    'imagem_capa',
])]
class Estabelecimento extends Model
{
    use BelongsToMunicipality, HasFactory, SoftDeletes;

    protected $table = 'estabelecimentos';

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'tem_selo_qualidade' => 'boolean',
            'validado_em' => 'datetime',
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

    public function getBusinessTypeAttribute(): ?string
    {
        return $this->attributes['tipo_estabelecimento'] ?? null;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->attributes['endereco'] ?? null;
    }

    public function getNeighborhoodAttribute(): ?string
    {
        return $this->attributes['bairro'] ?? null;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->attributes['telefone'] ?? null;
    }

    public function getPriceRangeAttribute(): ?string
    {
        return $this->attributes['faixa_preco'] ?? null;
    }

    public function getHasSealOfQualityAttribute(): bool
    {
        return (bool) ($this->attributes['tem_selo_qualidade'] ?? false);
    }
}
