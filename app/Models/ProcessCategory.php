<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProcessCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A group of processes. `name` is one language. The description is per language in the translations,
 * while the address (`slug`), the picture, the order, and the on or off switch are shared by all languages.
 */
class ProcessCategory extends Model
{
    /** @use HasFactory<ProcessCategoryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image_path', 'position', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /**
     * @return HasMany<ProcessCategoryTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProcessCategoryTranslation::class);
    }

    /**
     * @return HasMany<Process, $this>
     */
    public function processes(): HasMany
    {
        return $this->hasMany(Process::class);
    }
}
