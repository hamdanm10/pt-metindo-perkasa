<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ArticleType;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    protected $fillable = ['type', 'thumbnail_path', 'created_by'];

    /** @var array<string, string> */
    protected $casts = ['type' => ArticleType::class];

    /**
     * @return HasMany<ArticleTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ArticleTranslation::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The title shown in admin lists: the first written language in landing order.
     * Uses the loaded translations, so the list stays free of extra queries.
     */
    public function displayTitle(): ?string
    {
        foreach (config()->array('locales.landing') as $locale) {
            $translation = $this->translations->firstWhere('locale', $locale);

            if ($translation !== null) {
                return $translation->title;
            }
        }

        return null;
    }
}
