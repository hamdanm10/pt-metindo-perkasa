<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One question of the chat assistant. An answer can lead to follow-up questions, which are the
 * entries whose `parent_id` is this one, to any depth. The text is per language in the translations.
 */
class ChatEntry extends Model
{
    protected $fillable = ['parent_id', 'position'];

    /**
     * @return BelongsTo<ChatEntry, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<ChatEntryTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ChatEntryTranslation::class);
    }
}
