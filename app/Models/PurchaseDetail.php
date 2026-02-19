<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseDetail extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the purchase that owns this detail.
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the product for this detail.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
