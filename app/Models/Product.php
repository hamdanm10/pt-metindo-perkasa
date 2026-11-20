<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A chemical or product used in the steps of a process. `name` is one language; the explanation is per language
 * in the translations. Brochure, video, price, and the TDS file are shared by all languages, and the shop links
 * (one per marketplace) are in `links`.
 */
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'brochure_url', 'video_url', 'price', 'tds_path', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'price' => 'integer'];
    }

    /**
     * @return HasMany<ProductTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    /**
     * @return HasMany<ProductLink, $this>
     */
    public function links(): HasMany
    {
        return $this->hasMany(ProductLink::class)->orderBy('position');
    }
}
