<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcessStep extends Model
{
    protected $fillable = ['process_id', 'name', 'position'];

    /**
     * @return BelongsTo<Process, $this>
     */
    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    /**
     * @return HasMany<ProcessStepTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProcessStepTranslation::class);
    }

    /**
     * @return BelongsToMany<Product, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'process_step_product')->withPivot('position')->orderByPivot('position');
    }
}
