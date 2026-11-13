<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Landing\LandingContact;
use Database\Factories\WhatsappAgentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A customer service number of the WhatsApp button on the landing. `number` is digits only, with the
 * country code and no plus sign. Any change clears the cached list the landing reads.
 */
class WhatsappAgent extends Model
{
    /** @use HasFactory<WhatsappAgentFactory> */
    use HasFactory;

    protected $fillable = ['name', 'number', 'position', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        $flush = fn () => app(LandingContact::class)->flush();

        static::saved($flush);
        static::deleted($flush);
    }
}
