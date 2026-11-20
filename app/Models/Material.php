<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MaterialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A chemical or product used in the steps of a process. `name` is one language; the explanation is per language
 * in the translations. Brochure, video, price, and the TDS file are shared by all languages, and the shop links
 * (one per marketplace) are in `links`.
 */
class Material extends Model
{
    /** @use HasFactory<MaterialFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'brochure_url', 'video_url', 'price', 'tds_path', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'price' => 'integer'];
    }

    /**
     * @return HasMany<MaterialTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(MaterialTranslation::class);
    }

    /**
     * @return HasMany<MaterialLink, $this>
     */
    public function links(): HasMany
    {
        return $this->hasMany(MaterialLink::class)->orderBy('position');
    }
}
