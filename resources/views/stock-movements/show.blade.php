<x-app-layout>
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Stock Movement Details</h2>
                    <a href="{{ route('stock-movements.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Back to History
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">ID</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $movement->id }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Product</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $movement->product->name }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Type</h3>
                            <div class="mt-1">
                                @if($movement->type === 'ENTRADA')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Entrada (Purchase)
                                    </span>
                                @elseif($movement->type === 'SALIDA')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Salida (Sale)
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Ajuste (Adjustment)
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Quantity</h3>
                            <p class="mt-1 text-lg font-medium {{ $movement->type === 'SALIDA' ? 'text-red-600' : 'text-green-600' }}">
                                {{ $movement->type === 'SALIDA' ? '-' : '+' }}{{ $movement->quantity }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Reference</h3>
                            <p class="mt-1 text-gray-900">{{ $movement->reference ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">User</h3>
                            <p class="mt-1 text-gray-900">{{ $movement->user->name }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                                <p class="mt-1 text-gray-900">{{ $movement->created_at->format('M d, Y H:i') }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Updated At</h3>
                                <p class="mt-1 text-gray-900">{{ $movement->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg mt-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Audit Information</h3>
                            <p class="text-sm text-gray-600">
                                This movement was automatically recorded by the system as part of the stock audit trail.
                                Stock movements are immutable to maintain data integrity.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex space-x-3">
                    <a href="{{ route('stock-movements.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Back to History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
