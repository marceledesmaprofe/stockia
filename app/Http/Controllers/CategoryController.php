<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        try {
            $query = Category::with('products')->where('user_id', auth()->id());

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filtering by status
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            $categories = $query->orderBy($sortBy, $sortOrder)->get();

            return view('categories.index', compact('categories'));
        } catch (\Exception $e) {
            \Log::error('Error fetching categories: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error loading the categories. Please try again later.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string|max:1000',
                'status' => 'required|boolean',
                'business_id' => 'required|integer|min:1'
            ], [
                'name.required' => 'The category name is required.',
                'name.unique' => 'A category with this name already exists.',
                'name.max' => 'The category name may not be greater than 255 characters.',
                'business_id.min' => 'Business ID must be at least 1.'
            ]);

            // Add the authenticated user's ID to the validated data
            $validatedData['user_id'] = auth()->id();
            
            $category = Category::create($validatedData);

            return redirect()->route('categories.index')->with('success', 'Category "' . $category->name . '" created successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            \Log::error('Error creating category: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error creating the category. Please try again later.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $category = Category::with('products')->findOrFail($id);
            $this->authorize('view', $category);
            return view('categories.show', compact('category'));
        } catch (\Exception $e) {
            \Log::error('Error showing category: ' . $e->getMessage());
            return redirect()->route('categories.index')->with('error', 'Category not found or an error occurred.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $this->authorize('update', $category);
            return view('categories.edit', compact('category'));
        } catch (\Exception $e) {
            \Log::error('Error showing edit category form: ' . $e->getMessage());
            return redirect()->route('categories.index')->with('error', 'Category not found or an error occurred.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $this->authorize('update', $category);

            $validatedData = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories')->ignore($category->id)
                ],
                'description' => 'nullable|string|max:1000',
                'status' => 'required|boolean',
                'business_id' => 'required|integer|min:1'
            ], [
                'name.required' => 'The category name is required.',
                'name.unique' => 'A category with this name already exists.',
                'name.max' => 'The category name may not be greater than 255 characters.',
                'business_id.min' => 'Business ID must be at least 1.'
            ]);

            // Ensure the user_id is set to the authenticated user's ID
            $validatedData['user_id'] = auth()->id();
            
            $category->update($validatedData);

            return redirect()->route('categories.index')->with('success', 'Category "' . $category->name . '" updated successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            \Log::error('Error updating category: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error updating the category. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $this->authorize('delete', $category);

            // Check if category has associated products
            if ($category->products()->count() > 0) {
                return redirect()->route('categories.index')
                    ->with('error', 'Cannot delete category "' . $category->name . '" because it has ' . $category->products()->count() . ' associated product(s).');
            }

            $categoryName = $category->name;
            $category->delete();

            return redirect()->route('categories.index')->with('success', 'Category "' . $categoryName . '" deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting category: ' . $e->getMessage());
            return redirect()->route('categories.index')->with('error', 'There was an error deleting the category. Please try again later.');
        }
    }

    /**
     * Export categories to CSV
     */
    public function exportCsv()
    {
        $this->authorize('viewAny', Category::class);

        $filename = 'categories_' . now()->format('Y-m-d_H-i-s') . '.csv';
        return (new Category())->exportToCsv(Category::with('products')->where('user_id', auth()->id()), $filename);
    }

    /**
     * Export categories to PDF
     */
    public function exportPdf()
    {
        $this->authorize('viewAny', Category::class);

        $filename = 'categories_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        return (new Category())->exportToPdf(Category::with('products')->where('user_id', auth()->id()), $filename, 'Categories Report');
    }
}
