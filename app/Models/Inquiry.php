<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\InquiryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A question a customer asked the company, with the answer, kept so others do not have to ask it again.
 */
class Inquiry extends Model
{
    /** @use HasFactory<InquiryFactory> */
    use HasFactory;

    public const MAX_IMAGES = 10;

    protected $fillable = ['question', 'answer', 'asked_at', 'service_id', 'product_id', 'created_by'];

    /** @var array<string, string> */
    protected $casts = ['asked_at' => 'date'];

    /**
     * @return HasMany<InquiryImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(InquiryImage::class)->orderBy('id');
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
