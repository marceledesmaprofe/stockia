<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\RecordsStockMovements;

class StockAdjustment extends Model
{
    use RecordsStockMovements;

    protected $fillable = [
        'adjustment_date',
        'reason',
        'observations',
        'status',
        'user_id',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that created this adjustment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all details for this adjustment.
     */
    public function details(): HasMany
    {
        return $this->hasMany(StockAdjustmentDetail::class);
    }

    /**
     * Scope to filter adjustments by reason.
     */
    public function scopeOfReason($query, string $reason)
    {
        return $query->where('reason', $reason);
    }

    /**
     * Scope to filter adjustments by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter adjustments by date range.
     */
    public function scopeBetweenDates($query, string $from, string $to)
    {
        return $query->whereBetween('adjustment_date', [$from, $to]);
    }

    /**
     * Register stock movements for this adjustment.
     * Creates ENTRADA or SALIDA movement based on quantity sign.
     */
    public function registerStockMovements(): void
    {
        foreach ($this->details as $detail) {
            if ($detail->quantity > 0) {
                // Positive quantity = stock entry
                $this->registerStockEntry(
                    $detail->product_id,
                    abs($detail->quantity),
                    "Adjustment #{$this->id} - {$this->reason}"
                );
            } else {
                // Negative quantity = stock exit
                $this->registerStockExit(
                    $detail->product_id,
                    abs($detail->quantity),
                    "Adjustment #{$this->id} - {$this->reason}"
                );
            }
        }
    }

    /**
     * Reverse stock movements for this adjustment (when annulling).
     * Creates opposite movement to restore stock.
     */
    public function reverseStockMovements(): void
    {
        foreach ($this->details as $detail) {
            if ($detail->quantity > 0) {
                // Was entry, now exit to reverse
                $this->registerStockExit(
                    $detail->product_id,
                    abs($detail->quantity),
                    "Adjustment #{$this->id} - Anulled"
                );

                // Update current_stock in products table (remove stock)
                Product::where('id', $detail->product_id)
                    ->decrement('current_stock', abs($detail->quantity));
            } else {
                // Was exit, now entry to reverse
                $this->registerStockEntry(
                    $detail->product_id,
                    abs($detail->quantity),
                    "Adjustment #{$this->id} - Anulled"
                );

                // Update current_stock in products table (restore stock)
                Product::where('id', $detail->product_id)
                    ->increment('current_stock', abs($detail->quantity));
            }
        }
    }
}
