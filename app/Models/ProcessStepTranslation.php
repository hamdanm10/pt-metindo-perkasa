<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessStepTranslation extends Model
{
    protected $fillable = ['process_step_id', 'locale', 'description'];

    /**
     * @return BelongsTo<ProcessStep, $this>
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(ProcessStep::class, 'process_step_id');
    }
}
