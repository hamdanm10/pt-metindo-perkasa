<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A service of the company. `name` is the label admins and staff see in lists and pickers. What the landing
 * shows (title, summary, description, included, fit) is per language in the translations, while the
 * address (`slug`), the picture, the order, and the on or off switch are shared by all languages.
 */
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image_path', 'position', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /**
     * @return HasMany<ServiceTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ServiceTranslation::class);
    }
}
