<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessCategoryTranslation extends Model
{
    protected $fillable = ['process_category_id', 'locale', 'description'];

    /**
     * @return BelongsTo<ProcessCategory, $this>
     */
    public function processCategory(): BelongsTo
    {
        return $this->belongsTo(ProcessCategory::class);
    }
}
