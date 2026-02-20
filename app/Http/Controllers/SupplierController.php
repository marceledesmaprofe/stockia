<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SupplierController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Supplier::class);

        try {
            $query = Supplier::where('user_id', auth()->id());

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->search($searchTerm);
            }

            // Filtering by status
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            $suppliers = $query->orderBy($sortBy, $sortOrder)->get();

            return view('suppliers.index', compact('suppliers'));
        } catch (\Exception $e) {
            \Log::error('Error fetching suppliers: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error loading the suppliers. Please try again later.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('suppliers.create');
        } catch (\Exception $e) {
            \Log::error('Error showing create supplier form: ' . $e->getMessage());
            return redirect()->route('suppliers.index')->with('error', 'There was an error loading the form. Please try again later.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'document_type' => 'nullable|string|max:50',
                'document_number' => 'nullable|string|max:50|unique:suppliers,document_number,NULL,id,user_id,' . auth()->id(),
                'full_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'status' => 'required|boolean'
            ], [
                'full_name.required' => 'The supplier name is required.',
                'full_name.max' => 'The supplier name may not be greater than 255 characters.',
                'document_number.unique' => 'This document number is already registered for another supplier.',
                'email.email' => 'Please enter a valid email address.'
            ]);

            // Add the authenticated user's ID to the validated data
            $validatedData['user_id'] = auth()->id();

            $supplier = Supplier::create($validatedData);

            return redirect()->route('suppliers.index')->with('success', 'Supplier "' . $supplier->full_name . '" created successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            \Log::error('Error creating supplier: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error creating the supplier. Please try again later.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $this->authorize('view', $supplier);
            
            // Get supplier's purchases
            $purchases = $supplier->purchases()->with('details.product')->orderBy('purchase_date', 'desc')->get();
            
            return view('suppliers.show', compact('supplier', 'purchases'));
        } catch (\Exception $e) {
            \Log::error('Error showing supplier: ' . $e->getMessage());
            return redirect()->route('suppliers.index')->with('error', 'Supplier not found or an error occurred.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $this->authorize('update', $supplier);
            return view('suppliers.edit', compact('supplier'));
        } catch (\Exception $e) {
            \Log::error('Error showing edit supplier form: ' . $e->getMessage());
            return redirect()->route('suppliers.index')->with('error', 'Supplier not found or an error occurred.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $this->authorize('update', $supplier);

            $validatedData = $request->validate([
                'document_type' => 'nullable|string|max:50',
                'document_number' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::unique('suppliers')->ignore($supplier->id)->where(function ($query) {
                        return $query->where('user_id', auth()->id());
                    })
                ],
                'full_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'status' => 'required|boolean'
            ], [
                'full_name.required' => 'The supplier name is required.',
                'full_name.max' => 'The supplier name may not be greater than 255 characters.',
                'document_number.unique' => 'This document number is already registered for another supplier.',
                'email.email' => 'Please enter a valid email address.'
            ]);

            // Ensure the user_id is set to the authenticated user's ID
            $validatedData['user_id'] = auth()->id();

            $supplier->update($validatedData);

            return redirect()->route('suppliers.index')->with('success', 'Supplier "' . $supplier->full_name . '" updated successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            \Log::error('Error updating supplier: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error updating the supplier. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $this->authorize('delete', $supplier);
            $supplierName = $supplier->full_name;
            $supplier->delete();

            return redirect()->route('suppliers.index')->with('success', 'Supplier "' . $supplierName . '" deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting supplier: ' . $e->getMessage());
            return redirect()->route('suppliers.index')->with('error', 'There was an error deleting the supplier. Please try again later.');
        }
    }

    /**
     * Export suppliers to CSV
     */
    public function exportCsv()
    {
        $this->authorize('viewAny', Supplier::class);

        $filename = 'suppliers_' . now()->format('Y-m-d_H-i-s') . '.csv';
        return (new Supplier())->exportToCsv(Supplier::where('user_id', auth()->id()), $filename);
    }

    /**
     * Export suppliers to PDF
     */
    public function exportPdf()
    {
        $this->authorize('viewAny', Supplier::class);

        $filename = 'suppliers_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        return (new Supplier())->exportToPdf(Supplier::where('user_id', auth()->id()), $filename, 'Suppliers Report');
    }

    /**
     * Get suppliers for autocomplete (used in purchases form)
     */
    public function search(Request $request)
    {
        try {
            $query = Supplier::where('user_id', auth()->id())
                ->where('status', true);

            if ($request->filled('term')) {
                $query->search($request->input('term'));
            }

            $suppliers = $query->limit(10)->get(['id', 'full_name', 'document_number', 'email']);

            return response()->json($suppliers);
        } catch (\Exception $e) {
            \Log::error('Error searching suppliers: ' . $e->getMessage());
            return response()->json(['error' => 'Error searching suppliers'], 500);
        }
    }
}
