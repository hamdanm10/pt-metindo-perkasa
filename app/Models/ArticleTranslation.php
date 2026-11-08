<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PublicationStatus;
use Database\Factories\ArticleTranslationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleTranslation extends Model
{
    /** @use HasFactory<ArticleTranslationFactory> */
    use HasFactory;

    protected $fillable = [
        'article_id', 'locale', 'slug', 'title', 'excerpt', 'body', 'body_html',
        'meta_title', 'meta_description', 'image_alt', 'status', 'published_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'body' => 'array',
        'status' => PublicationStatus::class,
        'published_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Article, $this>
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublicationStatus::Published);
    }
}
