<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StockMovementController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = StockMovement::with(['product', 'user'])
                ->whereHas('product', function ($q) {
                    $q->where('user_id', auth()->id());
                });

            // Filter by product
            if ($request->filled('product_id')) {
                $query->where('product_id', $request->input('product_id'));
            }

            // Filter by type
            if ($request->filled('type')) {
                $query->where('type', $request->input('type'));
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->input('date_to'));
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            $movements = $query->orderBy($sortBy, $sortOrder)->paginate(20);

            // Get user's products for filter dropdown
            $products = Product::where('user_id', auth()->id())->get();

            return view('stock-movements.index', compact('movements', 'products'));
        } catch (\Exception $e) {
            \Log::error('Error fetching stock movements: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error loading the stock movements. Please try again later.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $movement = StockMovement::with(['product', 'user'])->findOrFail($id);

            // Verify the movement belongs to user's product
            if ($movement->product->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to stock movement.');
            }

            return view('stock-movements.show', compact('movement'));
        } catch (\Exception $e) {
            \Log::error('Error showing stock movement: ' . $e->getMessage());
            return redirect()->route('stock-movements.index')->with('error', 'Stock movement not found or an error occurred.');
        }
    }
}
