<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CustomerController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        try {
            $query = Customer::where('user_id', auth()->id());

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

            $customers = $query->orderBy($sortBy, $sortOrder)->get();

            return view('customers.index', compact('customers'));
        } catch (\Exception $e) {
            \Log::error('Error fetching customers: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error loading the customers. Please try again later.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('customers.create');
        } catch (\Exception $e) {
            \Log::error('Error showing create customer form: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'There was an error loading the form. Please try again later.');
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
                'document_number' => 'nullable|string|max:50|unique:customers,document_number,NULL,id,user_id,' . auth()->id(),
                'full_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'status' => 'required|boolean'
            ], [
                'full_name.required' => 'The customer name is required.',
                'full_name.max' => 'The customer name may not be greater than 255 characters.',
                'document_number.unique' => 'This document number is already registered for another customer.',
                'email.email' => 'Please enter a valid email address.'
            ]);

            // Add the authenticated user's ID to the validated data
            $validatedData['user_id'] = auth()->id();

            $customer = Customer::create($validatedData);

            return redirect()->route('customers.index')->with('success', 'Customer "' . $customer->full_name . '" created successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            \Log::error('Error creating customer: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error creating the customer. Please try again later.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $this->authorize('view', $customer);
            
            // Get customer's sales
            $sales = $customer->sales()->with('details.product')->orderBy('sale_date', 'desc')->get();
            
            return view('customers.show', compact('customer', 'sales'));
        } catch (\Exception $e) {
            \Log::error('Error showing customer: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'Customer not found or an error occurred.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $this->authorize('update', $customer);
            return view('customers.edit', compact('customer'));
        } catch (\Exception $e) {
            \Log::error('Error showing edit customer form: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'Customer not found or an error occurred.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $this->authorize('update', $customer);

            $validatedData = $request->validate([
                'document_type' => 'nullable|string|max:50',
                'document_number' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::unique('customers')->ignore($customer->id)->where(function ($query) {
                        return $query->where('user_id', auth()->id());
                    })
                ],
                'full_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'status' => 'required|boolean'
            ], [
                'full_name.required' => 'The customer name is required.',
                'full_name.max' => 'The customer name may not be greater than 255 characters.',
                'document_number.unique' => 'This document number is already registered for another customer.',
                'email.email' => 'Please enter a valid email address.'
            ]);

            // Ensure the user_id is set to the authenticated user's ID
            $validatedData['user_id'] = auth()->id();

            $customer->update($validatedData);

            return redirect()->route('customers.index')->with('success', 'Customer "' . $customer->full_name . '" updated successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            \Log::error('Error updating customer: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error updating the customer. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $this->authorize('delete', $customer);
            $customerName = $customer->full_name;
            $customer->delete();

            return redirect()->route('customers.index')->with('success', 'Customer "' . $customerName . '" deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting customer: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'There was an error deleting the customer. Please try again later.');
        }
    }

    /**
     * Export customers to CSV
     */
    public function exportCsv()
    {
        $this->authorize('viewAny', Customer::class);

        $filename = 'customers_' . now()->format('Y-m-d_H-i-s') . '.csv';
        return (new Customer())->exportToCsv(Customer::where('user_id', auth()->id()), $filename);
    }

    /**
     * Export customers to PDF
     */
    public function exportPdf()
    {
        $this->authorize('viewAny', Customer::class);

        $filename = 'customers_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        return (new Customer())->exportToPdf(Customer::where('user_id', auth()->id()), $filename, 'Customers Report');
    }

    /**
     * Get customers for autocomplete (used in sales form)
     */
    public function search(Request $request)
    {
        try {
            $query = Customer::where('user_id', auth()->id())
                ->where('status', true);

            if ($request->filled('term')) {
                $query->search($request->input('term'));
            }

            $customers = $query->limit(10)->get(['id', 'full_name', 'document_number', 'email']);

            return response()->json($customers);
        } catch (\Exception $e) {
            \Log::error('Error searching customers: ' . $e->getMessage());
            return response()->json(['error' => 'Error searching customers'], 500);
        }
    }
}
