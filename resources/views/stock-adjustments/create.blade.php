<x-app-layout>
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">New Stock Adjustment</h2>

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('stock-adjustments.store') }}" method="POST" id="adjustment-form">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label for="adjustment_date" class="block text-sm font-medium text-gray-700 mb-1">Adjustment Date *</label>
                            <input type="date" name="adjustment_date" id="adjustment_date" value="{{ old('adjustment_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('adjustment_date') border-red-500 @enderror" required>
                            @error('adjustment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                            <select name="reason" id="reason" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('reason') border-red-500 @enderror" required>
                                <option value="">Select...</option>
                                <option value="INVENTARIO" {{ old('reason') == 'INVENTARIO' ? 'selected' : '' }}>Inventario</option>
                                <option value="ROTURA" {{ old('reason') == 'ROTURA' ? 'selected' : '' }}>Rotura</option>
                                <option value="ROBO" {{ old('reason') == 'ROBO' ? 'selected' : '' }}>Robo</option>
                                <option value="ERROR" {{ old('reason') == 'ERROR' ? 'selected' : '' }}>Error</option>
                                <option value="INICIAL" {{ old('reason') == 'INICIAL' ? 'selected' : '' }}>Inicial</option>
                            </select>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="observations" class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                            <input type="text" name="observations" id="observations" value="{{ old('observations') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('observations') border-red-500 @enderror">
                            @error('observations')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-yellow-800">
                            <strong>Note:</strong> 
                            <span class="text-green-600">Inventario, Inicial</span> will <strong>add</strong> stock (ENTRADA).
                            <span class="text-red-600">Rotura, Robo, Error</span> will <strong>remove</strong> stock (SALIDA).
                        </p>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Products</h3>

                    <div id="products-container" class="space-y-4 mb-6">
                        <div class="product-row grid grid-cols-12 gap-2 items-end">
                            <div class="col-span-7">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                <input type="hidden" name="products[0][product_id]" class="product-id-input" value="">
                                <select class="product-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Select a product...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}">
                                            {{ $product->name }} (Current Stock: {{ $product->current_stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                                <input type="number" name="products[0][quantity]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" min="1" value="1" required>
                            </div>
                            <div class="col-span-1">
                                <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded transition duration-300" style="display: none;">×</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-product" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded transition duration-300 mb-6">
                        + Add Product
                    </button>

                    <div class="flex items-center justify-end mt-6 space-x-3">
                        <a href="{{ route('stock-adjustments.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Create Adjustment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = 1;

    // Add product row
    document.getElementById('add-product').addEventListener('click', function() {
        const container = document.getElementById('products-container');
        const newRow = document.createElement('div');
        newRow.className = 'product-row grid grid-cols-12 gap-2 items-end';
        newRow.innerHTML = `
            <div class="col-span-7">
                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                <input type="hidden" name="products[${productIndex}][product_id]" class="product-id-input" value="">
                <select class="product-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select a product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}">
                            {{ $product->name }} (Current Stock: {{ $product->current_stock }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                <input type="number" name="products[${productIndex}][quantity]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" min="1" value="1" required>
            </div>
            <div class="col-span-1">
                <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded transition duration-300">×</button>
            </div>
        `;
        container.appendChild(newRow);
        productIndex++;

        // Add event listeners to new row
        addRowEventListeners(newRow);
    });

    function addRowEventListeners(row) {
        // Product change - update hidden input and show current stock
        row.querySelector('.product-select').addEventListener('change', function() {
            const productId = this.value;
            row.querySelector('.product-id-input').value = productId;
        });

        // Remove row
        row.querySelector('.remove-row').addEventListener('click', function() {
            if (document.querySelectorAll('.product-row').length > 1) {
                row.remove();
            }
        });
    }

    // Add event listeners to initial row
    document.querySelectorAll('.product-row').forEach(row => {
        addRowEventListeners(row);
        // Initialize hidden input with current select value
        const select = row.querySelector('.product-select');
        const hiddenInput = row.querySelector('.product-id-input');
        if (select.value) {
            hiddenInput.value = select.value;
        }
    });
});
</script>
</x-app-layout>
