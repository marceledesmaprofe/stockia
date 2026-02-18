<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Exportable;

class Product extends Model
{
    use Exportable;

    protected $fillable = [
        'name',
        'description',
        'barcode',
        'current_stock',
        'sale_price',
        'status',
        'user_id',
        'category_id'
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'current_stock' => 'integer',
        'status' => 'boolean',
        'user_id' => 'integer',
        'category_id' => 'integer'
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user that owns the product.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all stock movements for this product.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the current stock calculated from movements.
     * This is the source of truth for stock quantity.
     */
    public function getCurrentStockFromMovementsAttribute(): int
    {
        return StockMovement::calculateStockForProduct($this->id);
    }

    /**
     * Scope to filter products by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get headers for CSV export
     */
    public function getCsvHeaders(): array
    {
        return [
            'id',
            'name',
            'description',
            'barcode',
            'category_name',
            'current_stock',
            'sale_price',
            'status',
            'user_id',
            'created_at',
            'updated_at'
        ];
    }
}
