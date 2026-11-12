<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatEntryTranslation extends Model
{
    protected $fillable = ['chat_entry_id', 'locale', 'question', 'answer', 'link_label', 'link_url'];

    /**
     * @return BelongsTo<ChatEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(ChatEntry::class, 'chat_entry_id');
    }
}
