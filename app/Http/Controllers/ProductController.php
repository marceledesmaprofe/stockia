<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        try {
            $query = Product::with('category');

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('barcode', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filtering by category
            if ($request->filled('category')) {
                $query->where('category_id', $request->input('category'));
            }

            // Filtering by status
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            // Filtering by stock level
            if ($request->filled('stock_level')) {
                switch ($request->input('stock_level')) {
                    case 'low':
                        $query->where('current_stock', '<', 10);
                        break;
                    case 'out_of_stock':
                        $query->where('current_stock', 0);
                        break;
                    case 'in_stock':
                        $query->where('current_stock', '>', 0);
                        break;
                }
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            $products = $query->orderBy($sortBy, $sortOrder)->get();

            // Get categories for filter dropdown
            $categories = Category::all();

            return view('products.index', compact('products', 'categories'));
        } catch (\Exception $e) {
            \Log::error('Error fetching products: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error loading the products. Please try again later.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            // Load categories for the form
            $categories = Category::all();
            return view('products.create', compact('categories'));
        } catch (\Exception $e) {
            \Log::error('Error showing create product form: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'There was an error loading the form. Please try again later.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'barcode' => 'nullable|string|unique:products,barcode|max:100',
                'category_id' => 'nullable|exists:categories,id',
                'current_stock' => 'required|integer|min:0|max:999999',
                'sale_price' => 'required|numeric|min:0|max:999999.99',
                'status' => 'required|boolean',
                'business_id' => 'required|integer|min:1'
            ], [
                'name.required' => 'The product name is required.',
                'name.max' => 'The product name may not be greater than 255 characters.',
                'current_stock.min' => 'Current stock cannot be negative.',
                'current_stock.max' => 'Current stock is too large.',
                'sale_price.min' => 'Sale price cannot be negative.',
                'sale_price.max' => 'Sale price is too large.',
                'category_id.exists' => 'The selected category is invalid.',
                'business_id.min' => 'Business ID must be at least 1.'
            ]);

            $product = Product::create($validatedData);

            return redirect()->route('products.index')->with('success', 'Product "' . $product->name . '" created successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            \Log::error('Error creating product: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error creating the product. Please try again later.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::with('category')->findOrFail($id);
            $this->authorize('view', $product);
            return view('products.show', compact('product'));
        } catch (\Exception $e) {
            \Log::error('Error showing product: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'Product not found or an error occurred.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('update', $product);
            $categories = Category::all();
            return view('products.edit', compact('product', 'categories'));
        } catch (\Exception $e) {
            \Log::error('Error showing edit product form: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'Product not found or an error occurred.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('update', $product);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'barcode' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::unique('products')->ignore($product->id)
                ],
                'category_id' => 'nullable|exists:categories,id',
                'current_stock' => 'required|integer|min:0|max:999999',
                'sale_price' => 'required|numeric|min:0|max:999999.99',
                'status' => 'required|boolean',
                'business_id' => 'required|integer|min:1'
            ], [
                'name.required' => 'The product name is required.',
                'name.max' => 'The product name may not be greater than 255 characters.',
                'current_stock.min' => 'Current stock cannot be negative.',
                'current_stock.max' => 'Current stock is too large.',
                'sale_price.min' => 'Sale price cannot be negative.',
                'sale_price.max' => 'Sale price is too large.',
                'category_id.exists' => 'The selected category is invalid.',
                'business_id.min' => 'Business ID must be at least 1.'
            ]);

            $product->update($validatedData);

            return redirect()->route('products.index')->with('success', 'Product "' . $product->name . '" updated successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            \Log::error('Error updating product: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error updating the product. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('delete', $product);
            $productName = $product->name;
            $product->delete();

            return redirect()->route('products.index')->with('success', 'Product "' . $productName . '" deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting product: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'There was an error deleting the product. Please try again later.');
        }
    }

    /**
     * Export products to CSV
     */
    public function exportCsv()
    {
        $this->authorize('viewAny', Product::class);

        $filename = 'products_' . now()->format('Y-m-d_H-i-s') . '.csv';
        return (new Product())->exportToCsv(Product::with('category'), $filename);
    }

    /**
     * Export products to PDF
     */
    public function exportPdf()
    {
        $this->authorize('viewAny', Product::class);

        $filename = 'products_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        return (new Product())->exportToPdf(Product::with('category'), $filename, 'Products Report');
    }
}
