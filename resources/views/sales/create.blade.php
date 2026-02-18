<x-app-layout>
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">New Sale</h2>

                <form action="{{ route('sales.store') }}" method="POST" id="sale-form">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                            <select name="customer_id" id="customer_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('customer_id') border-red-500 @enderror">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sale_date" class="block text-sm font-medium text-gray-700 mb-1">Sale Date *</label>
                            <input type="date" name="sale_date" id="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('sale_date') border-red-500 @enderror" required>
                            @error('sale_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                            <select name="payment_method" id="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('payment_method') border-red-500 @enderror" required>
                                <option value="">Select...</option>
                                <option value="EFECTIVO" {{ old('payment_method') == 'EFECTIVO' ? 'selected' : '' }}>Efectivo</option>
                                <option value="TRANSFERENCIA" {{ old('payment_method') == 'TRANSFERENCIA' ? 'selected' : '' }}>Transferencia</option>
                                <option value="TARJETA" {{ old('payment_method') == 'TARJETA' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="OTROS" {{ old('payment_method') == 'OTROS' ? 'selected' : '' }}>Otros</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                                <option value="">Select...</option>
                                <option value="PENDIENTE" {{ old('status') == 'PENDIENTE' ? 'selected' : '' }}>Pendiente</option>
                                <option value="PAGADA" {{ old('status') == 'PAGADA' ? 'selected' : '' }}>Pagada</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Products</h3>
                    
                    <div id="products-container" class="space-y-4 mb-6">
                        <div class="product-row grid grid-cols-12 gap-2 items-end">
                            <div class="col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                <select name="products[0][product_id]" class="product-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Select a product...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}" data-stock="{{ $product->current_stock }}">
                                            {{ $product->name }} (Stock: {{ $product->current_stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                                <input type="number" name="products[0][quantity]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" min="1" value="1" required>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                                <input type="number" name="products[0][unit_price]" class="unit-price-input w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" step="0.01" min="0" required>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                                <input type="text" class="subtotal-input w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50" readonly>
                            </div>
                            <div class="col-span-1">
                                <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded transition duration-300" style="display: none;">×</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-product" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded transition duration-300 mb-6">
                        + Add Product
                    </button>

                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-700">Total:</span>
                            <span id="total-amount" class="text-2xl font-bold text-gray-900">$0.00</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 space-x-3">
                        <a href="{{ route('sales.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Create Sale
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
    
    function calculateSubtotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
        const subtotal = quantity * unitPrice;
        row.querySelector('.subtotal-input').value = '$' + subtotal.toFixed(2);
        return subtotal;
    }
    
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            total += calculateSubtotal(row);
        });
        document.getElementById('total-amount').textContent = '$' + total.toFixed(2);
    }
    
    // Add product row
    document.getElementById('add-product').addEventListener('click', function() {
        const container = document.getElementById('products-container');
        const newRow = document.createElement('div');
        newRow.className = 'product-row grid grid-cols-12 gap-2 items-end';
        newRow.innerHTML = `
            <div class="col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                <select name="products[${productIndex}][product_id]" class="product-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select a product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}" data-stock="{{ $product->current_stock }}">
                            {{ $product->name }} (Stock: {{ $product->current_stock }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                <input type="number" name="products[${productIndex}][quantity]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" min="1" value="1" required>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                <input type="number" name="products[${productIndex}][unit_price]" class="unit-price-input w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" step="0.01" min="0" required>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                <input type="text" class="subtotal-input w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50" readonly>
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
        // Product change
        row.querySelector('.product-select').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const price = option.getAttribute('data-price');
            if (price) {
                row.querySelector('.unit-price-input').value = price;
                calculateSubtotal(row);
                calculateTotal();
            }
        });
        
        // Quantity change
        row.querySelector('.quantity-input').addEventListener('input', function() {
            calculateSubtotal(row);
            calculateTotal();
        });
        
        // Unit price change
        row.querySelector('.unit-price-input').addEventListener('input', function() {
            calculateSubtotal(row);
            calculateTotal();
        });
        
        // Remove row
        row.querySelector('.remove-row').addEventListener('click', function() {
            if (document.querySelectorAll('.product-row').length > 1) {
                row.remove();
                calculateTotal();
            }
        });
    }
    
    // Add event listeners to initial row
    document.querySelectorAll('.product-row').forEach(addRowEventListeners);
    
    // Calculate initial subtotal
    document.querySelectorAll('.product-row').forEach(calculateSubtotal);
    calculateTotal();
});
</script>
</x-app-layout>
