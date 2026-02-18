<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\User;
use App\Traits\RecordsStockMovements;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    use AuthorizesRequests, RecordsStockMovements;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Sale::with(['customer', 'user', 'details.product'])
                ->where('user_id', auth()->id());

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            // Filter by customer
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->input('customer_id'));
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('sale_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('sale_date', '<=', $request->input('date_to'));
            }

            // Filter by payment method
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->input('payment_method'));
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'sale_date');
            $sortOrder = $request->input('sort_order', 'desc');

            $sales = $query->orderBy($sortBy, $sortOrder)->paginate(20);

            // Get customers for filter dropdown
            $customers = User::all();

            return view('sales.index', compact('sales', 'customers'));
        } catch (\Exception $e) {
            \Log::error('Error fetching sales: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error loading the sales. Please try again later.');
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
            $customers = User::all();
            
            return view('sales.create', compact('products', 'customers'));
        } catch (\Exception $e) {
            \Log::error('Error showing create sale form: ' . $e->getMessage());
            return redirect()->route('sales.index')->with('error', 'There was an error loading the form. Please try again later.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'customer_id' => 'nullable|exists:users,id',
                'sale_date' => 'required|date',
                'payment_method' => 'required|in:EFECTIVO,TRANSFERENCIA,TARJETA,OTROS',
                'status' => 'required|in:PENDIENTE,PAGADA,ANULADA',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.unit_price' => 'required|numeric|min:0',
            ], [
                'customer_id.exists' => 'The selected customer does not exist.',
                'sale_date.required' => 'Sale date is required.',
                'sale_date.date' => 'Invalid date format.',
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
            foreach ($validatedData['products'] as &$product) {
                $product['subtotal'] = $product['quantity'] * $product['unit_price'];
                $total += $product['subtotal'];
            }

            // Verify product ownership and stock availability
            foreach ($validatedData['products'] as $productData) {
                $product = Product::where('id', $productData['product_id'])
                    ->where('user_id', auth()->id())
                    ->firstOrFail();

                // Check stock availability using current_stock field
                if ($product->current_stock < $productData['quantity']) {
                    throw ValidationException::withMessages([
                        "products.{$productData['product_id']}.quantity" =>
                        "Insufficient stock for product '{$product->name}'. Available: {$product->current_stock}, Requested: {$productData['quantity']}"
                    ]);
                }
            }

            // Create the sale
            $sale = Sale::create([
                'customer_id' => $validatedData['customer_id'],
                'sale_date' => $validatedData['sale_date'],
                'total' => $total,
                'status' => $validatedData['status'],
                'payment_method' => $validatedData['payment_method'],
                'user_id' => auth()->id(),
            ]);

            // Create sale details
            foreach ($validatedData['products'] as $productData) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    'subtotal' => $productData['subtotal'],
                ]);
            }

            // Register stock movements (SALIDA) for each product
            $sale->registerStockMovements();

            // Update current_stock in products table
            foreach ($validatedData['products'] as $productData) {
                Product::where('id', $productData['product_id'])
                    ->decrement('current_stock', $productData['quantity']);
            }

            DB::commit();

            return redirect()->route('sales.show', $sale->id)
                ->with('success', "Sale #{$sale->id} created successfully.");
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating sale: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error creating the sale. Please try again later.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $sale = Sale::with(['customer', 'user', 'details.product'])->findOrFail($id);

            // Verify the sale belongs to user
            if ($sale->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to sale.');
            }

            return view('sales.show', compact('sale'));
        } catch (\Exception $e) {
            \Log::error('Error showing sale: ' . $e->getMessage());
            return redirect()->route('sales.index')->with('error', 'Sale not found or an error occurred.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $sale = Sale::findOrFail($id);

            // Verify the sale belongs to user
            if ($sale->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to sale.');
            }

            // Sales cannot be edited after creation to maintain audit trail
            return redirect()->route('sales.show', $sale->id)
                ->with('info', 'Sales cannot be edited to maintain audit trail. You can only annul the sale.');
        } catch (\Exception $e) {
            \Log::error('Error showing edit sale form: ' . $e->getMessage());
            return redirect()->route('sales.index')->with('error', 'Sale not found or an error occurred.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Sales cannot be updated - redirect
        return redirect()->route('sales.show', $id)
            ->with('info', 'Sales cannot be updated to maintain audit trail.');
    }

    /**
     * Annull a sale (creates reverse stock movements).
     */
    public function annul(string $id)
    {
        DB::beginTransaction();
        try {
            $sale = Sale::findOrFail($id);

            // Verify the sale belongs to user
            if ($sale->user_id !== auth()->id()) {
                abort(403, 'Unauthorized access to sale.');
            }

            // Check if sale is already annulled
            if ($sale->status === 'ANULADA') {
                return redirect()->route('sales.show', $sale->id)
                    ->with('error', 'This sale is already annulled.');
            }

            // Reverse stock movements (ENTRADA to restore stock)
            $sale->reverseStockMovements();

            // Update sale status
            $sale->status = 'ANULADA';
            $sale->save();

            DB::commit();

            return redirect()->route('sales.show', $sale->id)
                ->with('success', "Sale #{$sale->id} has been annulled. Stock has been restored.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error annulling sale: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'There was an error annulling the sale. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Sales cannot be deleted to maintain audit trail
        return redirect()->route('sales.index')
            ->with('info', 'Sales cannot be deleted to maintain audit trail. You can annul a sale instead.');
    }
}
