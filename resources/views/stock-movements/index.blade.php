<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
                    <h2 class="text-2xl font-semibold text-gray-800">Stock Movements History</h2>
                    <p class="text-sm text-gray-500">
                        Audit trail of all stock changes from sales, purchases, and adjustments
                    </p>
                </div>

                <!-- Filters -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form method="GET" action="{{ route('stock-movements.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                            <select name="product_id" id="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Types</option>
                                <option value="ENTRADA" {{ request('type') == 'ENTRADA' ? 'selected' : '' }}>Entrada (Purchase)</option>
                                <option value="SALIDA" {{ request('type') == 'SALIDA' ? 'selected' : '' }}>Salida (Sale)</option>
                                <option value="AJUSTE" {{ request('type') == 'AJUSTE' ? 'selected' : '' }}>Ajuste (Adjustment)</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 text-white py-2 px-4 rounded-md transition duration-300 ease-in-out">
                                Apply Filters
                            </button>
                            <a href="{{ route('stock-movements.index') }}" class="ml-2 w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md transition duration-300 ease-in-out text-center">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>

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

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($movements as $movement)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $movement->product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($movement->type === 'ENTRADA')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Entrada
                                            </span>
                                        @elseif($movement->type === 'SALIDA')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Salida
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Ajuste
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $movement->type === 'SALIDA' ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $movement->type === 'SALIDA' ? '-' : '+' }}{{ $movement->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $movement->reference ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('stock-movements.show', $movement->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No stock movements found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($movements->hasPages())
                    <div class="mt-4">
                        {{ $movements->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>
