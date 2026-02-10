<x-app-layout>
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Product Details</h2>
                    <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Back to Products
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">ID</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $product->id }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Name</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $product->name }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Description</h3>
                            <p class="mt-1 text-gray-900">{{ $product->description ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Barcode</h3>
                            <p class="mt-1 text-gray-900">{{ $product->barcode ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Category</h3>
                            <p class="mt-1 text-gray-900">{{ $product->category ? $product->category->name : 'Uncategorized' }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Current Stock</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $product->current_stock }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Sale Price</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">${{ number_format($product->sale_price, 2) }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <div class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Business ID</h3>
                            <p class="mt-1 text-gray-900">{{ $product->business_id }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                                <p class="mt-1 text-gray-900">{{ $product->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Updated At</h3>
                                <p class="mt-1 text-gray-900">{{ $product->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex space-x-3">
                    <a href="{{ route('products.edit', $product->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        Edit
                    </a>
                    
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete
                        </button>
                    </form>
                    
                    <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>