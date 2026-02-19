<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Product;
use App\Models\User;
use App\Traits\RecordsStockMovements;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    use AuthorizesRequests, RecordsStockMovements;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Purchase::with(['supplier', 'user', 'details.product'])
                ->where('user_id', auth()->id());

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            // Filter by supplier
            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->input('supplier_id'));
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('purchase_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('purchase_date', '<=', $request->input('date_to'));
            }

            // Filter by payment method
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->input('payment_method'));
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'purchase_date');
            $sortOrder = $request->input('sort_order', 'desc');

            $purchases = $query->orderBy($sortBy, $sortOrder)->paginate(20);

            // Get suppliers for filter dropdown
            $suppliers = User::all();

            return view('purchases.index', compact('purchases', 'suppliers'));
        } catch (\Exception $e) {
            \Log::error('Error fetching purchases: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error loading the purchases. Please try again later.');
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
            $suppliers = User::all();

            return view('purchases.create', compact('products', 'suppliers'));
        } catch (\Exception $e) {
            \Log::error('Error showing create purchase form: ' . $e->getMessage());
            return redirect()->route('purchases.index')->with('error', 'There was an error loading the form. Please try again later.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            \Log::info('Purchase data:', $request->all());
            \Log::info('Products input:', ['products' => $request->input('products')]);

            $validatedData = $request->validate([
                'supplier_id' => 'nullable|exists:users,id',
                'purchase_date' => 'required|date',
                'payment_method' => 'required|in:EFECTIVO,TRANSFERENCIA,TARJETA,OTROS',
                'status' => 'required|in:PENDIENTE,PAGADA,ANULADA',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.unit_price' => 'required|numeric|min:0',
            ], [
                'supplier_id.exists' => 'The selected supplier does not exist.',
                'purchase_date.required' => 'Purchase date is required.',
                'purchase_date.date' => 'Invalid date format.',
                'payment_method.required' => 'Payment method is required.',
                'payment_method.in' => 'Invalid payment method.',
                'status.required' => 'Status is required.',
                'status.in' => 'Invalid status.',
                'products.required' => 'At least one product is required.',
                'products.array' => 'Products must be an array.',
                'products.min' => 'At least one product is required.',
                'products.*.product_id.required' => 'Product is required.',
                'products.*.product_id.exists' => 'The selected product does not exist.',
                'products.*.quantity.required' => 'Quantity is required.',
                'products.*.quantity.integer' => 'Quantity must be a whole number.',
                'products.*.quantity.min' => 'Quantity must be at least 1.',
                'products.*.unit_price.required' => 'Unit price is required.',
                'products.*.unit_price.numeric' => 'Unit price must be a number.',
                'products.*.unit_price.min' => 'Unit price cannot be negative.',
            ]);

            // Calculate total
            $total = 0;
            foreach ($validatedData['products'] as $key => $productItem) {
                $validatedData['products'][$key]['subtotal'] = $productItem['quantity'] * $productItem['unit_price'];
                $total += $validatedData['products'][$key]['subtotal'];
            }

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
            }

            // Create the purchase
            $purchase = Purchase::create([
                'supplier_id' => $validatedData['supplier_id'],
                'purchase_date' => $validatedData['purchase_date'],
                'total' => $total,
                'status' => $validatedData['status'],
                'payment_method' => $validatedData['payment_method'],
                'user_id' => auth()->id(),
            ]);

            // Create purchase details
            foreach ($validatedData['products'] as $key => $productData) {
                \Log::info('Creating detail', ['key' => $key, 'productData' => $productData]);
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    'subtotal' => $productData['subtotal'],
                ]);
            }

            // Register stock movements (ENTRADA) for each product
            $purchase->registerStockMovements();

            // Update current_stock in products table (increase stock)
            foreach ($validatedData['products'] as $productData) {
                Product::where('id', $productData['product_id'])
                    ->increment('current_stock', $productData['quantity']);
            }

            DB::commit();

            return redirect()->route('purchases.show', $purchase->id)
                ->with('success', "Purchase #{$purchase->id} created successfully.");
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating purchase: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error creating the purchase. Please try again later.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $purchase = Purchase::with(['supplier', 'user', 'details.product'])->findOrFail($id);

            // Verify the purchase belongs to user
            if ($purchase->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to purchase.');
            }

            return view('purchases.show', compact('purchase'));
        } catch (\Exception $e) {
            \Log::error('Error showing purchase: ' . $e->getMessage());
            return redirect()->route('purchases.index')->with('error', 'Purchase not found or an error occurred.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $purchase = Purchase::findOrFail($id);

            // Verify the purchase belongs to user
            if ($purchase->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to purchase.');
            }

            // Purchases cannot be edited after creation to maintain audit trail
            return redirect()->route('purchases.show', $purchase->id)
                ->with('info', 'Purchases cannot be edited to maintain audit trail. You can only annul the purchase.');
        } catch (\Exception $e) {
            \Log::error('Error showing edit purchase form: ' . $e->getMessage());
            return redirect()->route('purchases.index')->with('error', 'Purchase not found or an error occurred.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Purchases cannot be updated - redirect
        return redirect()->route('purchases.show', $id)
            ->with('info', 'Purchases cannot be updated to maintain audit trail.');
    }

    /**
     * Mark a pending purchase as paid.
     */
    public function markAsPaid(string $id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::findOrFail($id);

            // Verify the purchase belongs to user
            if ($purchase->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to purchase.');
            }

            // Check if purchase is already paid or annulled
            if ($purchase->status === 'PAGADA') {
                return redirect()->route('purchases.show', $purchase->id)
                    ->with('info', 'This purchase is already paid.');
            }

            if ($purchase->status === 'ANULADA') {
                return redirect()->route('purchases.show', $purchase->id)
                    ->with('error', 'Cannot mark an annulled purchase as paid.');
            }

            // Update purchase status
            $purchase->status = 'PAGADA';
            $purchase->save();

            DB::commit();

            return redirect()->route('purchases.show', $purchase->id)
                ->with('success', "Purchase #{$purchase->id} has been marked as paid.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error marking purchase as paid: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'There was an error marking the purchase as paid. Please try again later.');
        }
    }

    /**
     * Annull a purchase (creates reverse stock movements).
     */
    public function annul(string $id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::findOrFail($id);

            // Verify the purchase belongs to user
            if ($purchase->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to purchase.');
            }

            // Check if purchase is already annulled
            if ($purchase->status === 'ANULADA') {
                return redirect()->route('purchases.show', $purchase->id)
                    ->with('error', 'This purchase is already annulled.');
            }

            // Reverse stock movements (SALIDA to remove stock)
            $purchase->reverseStockMovements();

            // Update purchase status
            $purchase->status = 'ANULADA';
            $purchase->save();

            DB::commit();

            return redirect()->route('purchases.show', $purchase->id)
                ->with('success', "Purchase #{$purchase->id} has been annulled. Stock has been removed.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error annulling purchase: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'There was an error annulling the purchase. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Purchases cannot be deleted to maintain audit trail
        return redirect()->route('purchases.index')
            ->with('info', 'Purchases cannot be deleted to maintain audit trail. You can annul a purchase instead.');
    }
}
