<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\Product;
use App\Models\User;
use App\Traits\RecordsStockMovements;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    use AuthorizesRequests, RecordsStockMovements;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = StockAdjustment::with(['user', 'details.product'])
                ->where('user_id', auth()->id());

            // Filter by reason
            if ($request->filled('reason')) {
                $query->where('reason', $request->input('reason'));
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('adjustment_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('adjustment_date', '<=', $request->input('date_to'));
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'adjustment_date');
            $sortOrder = $request->input('sort_order', 'desc');

            $adjustments = $query->orderBy($sortBy, $sortOrder)->paginate(20);

            return view('stock-adjustments.index', compact('adjustments'));
        } catch (\Exception $e) {
            \Log::error('Error fetching stock adjustments: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error loading the stock adjustments. Please try again later.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $products = Product::where('user_id', auth()->id())
                ->where('status', 1)
                ->get();

            return view('stock-adjustments.create', compact('products'));
        } catch (\Exception $e) {
            \Log::error('Error showing create stock adjustment form: ' . $e->getMessage());
            return redirect()->route('stock-adjustments.index')->with('error', 'There was an error loading the form. Please try again later.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            \Log::info('Stock Adjustment data:', $request->all());
            \Log::info('Products input:', ['products' => $request->input('products')]);

            $validatedData = $request->validate([
                'adjustment_date' => 'required|date',
                'reason' => 'required|in:INVENTARIO,ROTURA,ROBO,ERROR,INICIAL',
                'observations' => 'nullable|string|max:1000',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ], [
                'adjustment_date.required' => 'Adjustment date is required.',
                'adjustment_date.date' => 'Invalid date format.',
                'reason.required' => 'Reason is required.',
                'reason.in' => 'Invalid reason.',
                'products.required' => 'At least one product is required.',
                'products.array' => 'Products must be an array.',
                'products.min' => 'At least one product is required.',
                'products.*.product_id.required' => 'Product is required.',
                'products.*.product_id.exists' => 'The selected product does not exist.',
                'products.*.quantity.required' => 'Quantity is required.',
                'products.*.quantity.integer' => 'Quantity must be a whole number.',
                'products.*.quantity.min' => 'Quantity must be at least 1.',
            ]);

            // Verify product ownership
            foreach ($validatedData['products'] as $productData) {
                $product = Product::where('id', $productData['product_id'])
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$product) {
                    \Log::error('Product not found or does not belong to user', ['product_id' => $productData['product_id'], 'user_id' => auth()->id()]);
                    throw ValidationException::withMessages([
                        "products.{$productData['product_id']}.product_id" =>
                        "The selected product is invalid or does not belong to you."
                    ]);
                }

                // Check stock availability for negative adjustments (exits)
                // Reasons that typically reduce stock: ROTURA, ROBO, ERROR
                $isExitReason = in_array($validatedData['reason'], ['ROTURA', 'ROBO', 'ERROR']);
                
                if ($isExitReason && $product->current_stock < $productData['quantity']) {
                    throw ValidationException::withMessages([
                        "products.{$productData['product_id']}.quantity" =>
                        "Insufficient stock for product '{$product->name}'. Available: {$product->current_stock}, Requested: {$productData['quantity']}"
                    ]);
                }
            }

            // Create the stock adjustment
            $adjustment = StockAdjustment::create([
                'adjustment_date' => $validatedData['adjustment_date'],
                'reason' => $validatedData['reason'],
                'observations' => $validatedData['observations'],
                'user_id' => auth()->id(),
            ]);

            // Create adjustment details
            // Determine if this is an entry or exit based on reason
            $isExitReason = in_array($validatedData['reason'], ['ROTURA', 'ROBO', 'ERROR']);
            
            foreach ($validatedData['products'] as $key => $productData) {
                // For exit reasons, store quantity as negative
                $quantity = $isExitReason ? -$productData['quantity'] : $productData['quantity'];
                
                \Log::info('Creating detail', ['key' => $key, 'productData' => $productData, 'quantity' => $quantity]);
                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $quantity,
                ]);
            }

            // Register stock movements (ENTRADA or SALIDA based on quantity sign)
            $adjustment->registerStockMovements();

            // Update current_stock in products table
            foreach ($validatedData['products'] as $productData) {
                $quantity = $isExitReason ? -$productData['quantity'] : $productData['quantity'];
                
                if ($quantity > 0) {
                    Product::where('id', $productData['product_id'])
                        ->increment('current_stock', $quantity);
                } else {
                    Product::where('id', $productData['product_id'])
                        ->decrement('current_stock', abs($quantity));
                }
            }

            DB::commit();

            return redirect()->route('stock-adjustments.show', $adjustment->id)
                ->with('success', "Stock Adjustment #{$adjustment->id} created successfully.");
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating stock adjustment: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error creating the stock adjustment. Please try again later.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $adjustment = StockAdjustment::with(['user', 'details.product'])->findOrFail($id);

            // Verify the adjustment belongs to user
            if ($adjustment->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to stock adjustment.');
            }

            return view('stock-adjustments.show', compact('adjustment'));
        } catch (\Exception $e) {
            \Log::error('Error showing stock adjustment: ' . $e->getMessage());
            return redirect()->route('stock-adjustments.index')->with('error', 'Stock adjustment not found or an error occurred.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $adjustment = StockAdjustment::findOrFail($id);

            // Verify the adjustment belongs to user
            if ($adjustment->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to stock adjustment.');
            }

            // Stock adjustments cannot be edited after creation to maintain audit trail
            return redirect()->route('stock-adjustments.show', $adjustment->id)
                ->with('info', 'Stock adjustments cannot be edited to maintain audit trail. You can only annul the adjustment.');
        } catch (\Exception $e) {
            \Log::error('Error showing edit stock adjustment form: ' . $e->getMessage());
            return redirect()->route('stock-adjustments.index')->with('error', 'Stock adjustment not found or an error occurred.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Stock adjustments cannot be updated - redirect
        return redirect()->route('stock-adjustments.show', $id)
            ->with('info', 'Stock adjustments cannot be updated to maintain audit trail.');
    }

    /**
     * Annull a stock adjustment (creates reverse stock movements).
     */
    public function annul(string $id)
    {
        DB::beginTransaction();
        try {
            $adjustment = StockAdjustment::findOrFail($id);

            // Verify the adjustment belongs to user
            if ($adjustment->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to stock adjustment.');
            }

            // Check if adjustment is already annulled
            if ($adjustment->status === 'ANULADA') {
                return redirect()->route('stock-adjustments.show', $adjustment->id)
                    ->with('error', 'This stock adjustment is already annulled.');
            }

            // Reverse stock movements
            $adjustment->reverseStockMovements();

            // Update adjustment status
            $adjustment->status = 'ANULADA';
            $adjustment->save();

            DB::commit();

            return redirect()->route('stock-adjustments.show', $adjustment->id)
                ->with('success', "Stock Adjustment #{$adjustment->id} has been annulled. Stock has been adjusted.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error annulling stock adjustment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'There was an error annulling the stock adjustment. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Stock adjustments cannot be deleted to maintain audit trail
        return redirect()->route('stock-adjustments.index')
            ->with('info', 'Stock adjustments cannot be deleted to maintain audit trail. You can annul an adjustment instead.');
    }
}
