<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\RecordsStockMovements;

class Purchase extends Model
{
    use RecordsStockMovements;

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'total',
        'status',
        'payment_method',
        'user_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Get the supplier that owns this purchase.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get the user that created this purchase.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all details for this purchase.
     */
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    /**
     * Scope to filter purchases by status.
     */
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter purchases by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter purchases by date range.
     */
    public function scopeBetweenDates($query, string $from, string $to)
    {
        return $query->whereBetween('purchase_date', [$from, $to]);
    }

    /**
     * Register stock movements for this purchase.
     * Creates an ENTRADA movement for each detail.
     */
    public function registerStockMovements(): void
    {
        foreach ($this->details as $detail) {
            $this->registerStockEntry(
                $detail->product_id,
                $detail->quantity,
                "Purchase #{$this->id}"
            );
        }
    }

    /**
     * Reverse stock movements for this purchase (when annulling).
     * Creates a SALIDA movement to remove stock.
     */
    public function reverseStockMovements(): void
    {
        foreach ($this->details as $detail) {
            $this->registerStockExit(
                $detail->product_id,
                $detail->quantity,
                "Purchase #{$this->id} - Anulled"
            );

            // Update current_stock in products table (remove stock)
            Product::where('id', $detail->product_id)
                ->decrement('current_stock', $detail->quantity);
        }
    }
}
