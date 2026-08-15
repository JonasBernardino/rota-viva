<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'uuid',
    'nome',
    'slug',
    'codigo_ibge',
    'uf',
    'nome_schema',
    'status',
    'fuso_horario',
    'configuracoes',
    'brand_name',
    'brand_logo_path',
    'hero_eyebrow',
    'hero_title',
    'hero_description',
    'hero_image_path',
    'hero_image_alt',
    'hero_search_placeholder',
    'hero_card_title',
    'hero_card_tags',
    'local_economy_eyebrow',
    'local_economy_title',
    'local_economy_description',
    'local_economy_stat',
    'local_economy_link_label',
    'local_economy_link_url',
    'local_economy_image_path',
    'local_economy_image_alt',
])]
class Municipio extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'municipios';

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'configuracoes' => 'array',
            'hero_card_tags' => 'array',
        ];
    }

    public function dominios(): HasMany
    {
        return $this->hasMany(DominioMunicipio::class, 'municipio_id');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(UsuarioPlataforma::class, 'municipio_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function brandName(): string
    {
        return $this->brand_name ?: 'ROTA VIVA';
    }

    public function brandLogoUrl(): ?string
    {
        return $this->publicAssetUrl($this->brand_logo_path);
    }

    public function heroImageUrl(): string
    {
        return $this->publicAssetUrl($this->hero_image_path)
            ?: asset('images/rota-viva-hero.webp');
    }

    public function localEconomyImageUrl(): string
    {
        return $this->publicAssetUrl($this->local_economy_image_path)
            ?: asset('images/local-artisan.webp');
    }

    public function publicAssetUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        return Storage::url($path);
    }
}
