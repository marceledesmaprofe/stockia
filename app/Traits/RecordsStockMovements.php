<?php

namespace App\Traits;

use App\Models\StockMovement;
use App\Models\Product;

trait RecordsStockMovements
{
    /**
     * Register a stock entry (ENTRADA).
     * Positive quantity is added to stock.
     *
     * @param int $productId
     * @param int $quantity
     * @param string|null $reference
     * @param int|null $userId
     * @return StockMovement
     */
    public function registerStockEntry(
        int $productId,
        int $quantity,
        ?string $reference = null,
        ?int $userId = null
    ): StockMovement {
        return $this->registerStockMovement(
            $productId,
            'ENTRADA',
            abs($quantity),
            $reference,
            $userId
        );
    }

    /**
     * Register a stock exit (SALIDA).
     * Positive quantity is subtracted from stock.
     *
     * @param int $productId
     * @param int $quantity
     * @param string|null $reference
     * @param int|null $userId
     * @return StockMovement
     */
    public function registerStockExit(
        int $productId,
        int $quantity,
        ?string $reference = null,
        ?int $userId = null
    ): StockMovement {
        // Validation is done in the controller using current_stock field
        return $this->registerStockMovement(
            $productId,
            'SALIDA',
            abs($quantity),
            $reference,
            $userId
        );
    }

    /**
     * Register a stock adjustment (AJUSTE).
     * Can be positive or negative.
     *
     * @param int $productId
     * @param int $quantity Positive to add, negative to subtract
     * @param string|null $reference
     * @param int|null $userId
     * @return StockMovement
     */
    public function registerStockAdjustment(
        int $productId,
        int $quantity,
        ?string $reference = null,
        ?int $userId = null
    ): StockMovement {
        if ($quantity < 0) {
            $currentStock = StockMovement::calculateStockForProduct($productId);

            if ($currentStock < abs($quantity)) {
                throw new \RuntimeException(
                    "Insufficient stock for adjustment. Current: {$currentStock}, Adjustment: " . $quantity
                );
            }
        }

        return $this->registerStockMovement(
            $productId,
            'AJUSTE',
            $quantity,
            $reference,
            $userId
        );
    }

    /**
     * Core method to register a stock movement.
     *
     * @param int $productId
     * @param string $type
     * @param int $quantity
     * @param string|null $reference
     * @param int|null $userId
     * @return StockMovement
     */
    private function registerStockMovement(
        int $productId,
        string $type,
        int $quantity,
        ?string $reference = null,
        ?int $userId = null
    ): StockMovement {
        return StockMovement::create([
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $quantity,
            'reference' => $reference,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

    /**
     * Get current stock for a product.
     *
     * @param int $productId
     * @return int
     */
    public function getCurrentStock(int $productId): int
    {
        return StockMovement::calculateStockForProduct($productId);
    }
}
