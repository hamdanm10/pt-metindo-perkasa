<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProcessFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One process of a category, such as Alkaline Zinc. `name` is one language and the description is per language
 * in the translations. The steps are the flow shown in the diagram, each with the products used in it.
 */
class Process extends Model
{
    /** @use HasFactory<ProcessFactory> */
    use HasFactory;

    protected $fillable = ['process_category_id', 'name', 'slug', 'position', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /**
     * @return BelongsTo<ProcessCategory, $this>
     */
    public function processCategory(): BelongsTo
    {
        return $this->belongsTo(ProcessCategory::class);
    }

    /**
     * @return HasMany<ProcessTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProcessTranslation::class);
    }

    /**
     * @return HasMany<ProcessStep, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(ProcessStep::class)->orderBy('position');
    }
}
