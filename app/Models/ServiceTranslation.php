<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTranslation extends Model
{
    protected $fillable = ['service_id', 'locale', 'title', 'summary', 'description', 'included', 'fit'];

    /** @var array<string, string> */
    protected $casts = ['included' => 'array'];

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
