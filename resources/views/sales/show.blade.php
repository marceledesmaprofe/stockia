<x-app-layout>
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Sale #{{ $sale->id }} Details</h2>
                    <a href="{{ route('sales.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Back to Sales
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                        {{ session('info') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Sale ID</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">#{{ $sale->id }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Date</h3>
                            <p class="mt-1 text-gray-900">{{ $sale->sale_date->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Customer</h3>
                            <p class="mt-1 text-gray-900">{{ $sale->customer ? $sale->customer->full_name : 'Walk-in Customer' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Payment Method</h3>
                            <p class="mt-1 text-gray-900">{{ $sale->payment_method ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <div class="mt-1">
                                @if($sale->status === 'PENDIENTE')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($sale->status === 'PAGADA')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Paid
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Annulled
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created By</h3>
                            <p class="mt-1 text-gray-900">{{ $sale->user->name }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                            <p class="mt-1 text-gray-900">{{ $sale->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-800 mb-4">Products</h3>
                
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sale->details as $detail)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail->product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $detail->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($detail->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format($detail->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Total:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">${{ number_format($sale->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Audit Information</h3>
                    <p class="text-sm text-gray-600">
                        This sale automatically generated stock movements (SALIDA) for each product.
                        If you need to correct an error, annul the sale to create reverse movements (ENTRADA).
                    </p>
                </div>

                <div class="flex space-x-3">
                    @if($sale->status === 'PENDIENTE')
                        <form action="{{ route('sales.mark-as-paid', $sale->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this sale as paid?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Mark as Paid
                            </button>
                        </form>
                    @endif

                    @if($sale->status !== 'ANULADA')
                        <form action="{{ route('sales.annul', $sale->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to annul this sale? This will restore the stock for all products.');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Annul Sale
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('sales.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Back to Sales
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
