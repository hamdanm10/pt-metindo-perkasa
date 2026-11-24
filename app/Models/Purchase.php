<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PurchaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A purchase made by a customer or a distributor: the products bought and the documents (invoice, delivery note) that go with it.
 */
class Purchase extends Model
{
    /** @use HasFactory<PurchaseFactory> */
    use HasFactory;

    public const MAX_FILES = 10;

    public const MAX_ITEMS = 50;

    protected $fillable = ['buyer_id', 'purchased_at', 'invoice_number', 'notes', 'created_by'];

    /** @var array<string, string> */
    protected $casts = ['purchased_at' => 'date'];

    /**
     * @return HasMany<PurchaseItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class)->orderBy('id');
    }

    /**
     * @return HasMany<PurchaseFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(PurchaseFile::class)->orderBy('id');
    }

    /**
     * The customer or distributor who bought.
     *
     * @return BelongsTo<User, $this>
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
