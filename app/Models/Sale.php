<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\RecordsStockMovements;

class Sale extends Model
{
    use RecordsStockMovements;

    protected $fillable = [
        'customer_id',
        'sale_date',
        'total',
        'status',
        'payment_method',
        'user_id',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Get the customer that owns this sale.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the user that created this sale.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all details for this sale.
     */
    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    /**
     * Scope to filter sales by status.
     */
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter sales by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter sales by date range.
     */
    public function scopeBetweenDates($query, string $from, string $to)
    {
        return $query->whereBetween('sale_date', [$from, $to]);
    }

    /**
     * Register stock movements for this sale.
     * Creates a SALIDA movement for each detail.
     */
    public function registerStockMovements(): void
    {
        foreach ($this->details as $detail) {
            $this->registerStockExit(
                $detail->product_id,
                $detail->quantity,
                "Sale #{$this->id}"
            );
        }
    }

    /**
     * Reverse stock movements for this sale (when annulling).
     * Creates an ENTRADA movement to restore stock.
     */
    public function reverseStockMovements(): void
    {
        foreach ($this->details as $detail) {
            $this->registerStockEntry(
                $detail->product_id,
                $detail->quantity,
                "Sale #{$this->id} - Anulled"
            );
        }
    }
}
