<x-app-layout>
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Supplier Details</h2>
                    <a href="{{ route('suppliers.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Back to Suppliers
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">ID</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $supplier->id }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Full Name</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $supplier->full_name }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Document</h3>
                            <p class="mt-1 text-gray-900">
                                @if($supplier->document_type && $supplier->document_number)
                                    <span class="font-semibold">{{ $supplier->document_type }}:</span> {{ $supplier->document_number }}
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Phone</h3>
                            <p class="mt-1 text-gray-900">{{ $supplier->phone ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Email</h3>
                            <p class="mt-1 text-gray-900">{{ $supplier->email ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Address</h3>
                            <p class="mt-1 text-gray-900">{{ $supplier->address ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <div class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $supplier->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $supplier->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Total Purchases</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $purchases->count() }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                                <p class="mt-1 text-gray-900">{{ $supplier->created_at->format('M d, Y H:i') }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Updated At</h3>
                                <p class="mt-1 text-gray-900">{{ $supplier->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex space-x-3">
                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        Edit
                    </a>

                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete
                        </button>
                    </form>
                </div>

                <!-- Purchases History -->
                @if($purchases->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Purchases History</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($purchases as $purchase)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $purchase->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($purchase->total, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $purchase->status === 'PAGADA' ? 'bg-green-100 text-green-800' : ($purchase->status === 'ANULADA' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ $purchase->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $purchase->payment_method }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('purchases.show', $purchase->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>
