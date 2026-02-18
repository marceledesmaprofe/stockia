<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reference',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the product that owns this movement.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user that created this movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter movements by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter movements by product.
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to filter movements by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Calculate the net stock change for a product.
     */
    public static function calculateStockForProduct(int $productId): int
    {
        return self::where('product_id', $productId)
            ->selectRaw('SUM(CASE 
                WHEN type = "ENTRADA" THEN quantity 
                WHEN type = "SALIDA" THEN -quantity 
                WHEN type = "AJUSTE" THEN quantity 
                ELSE 0 
            END) as total')
            ->value('total') ?? 0;
    }
}
