<x-app-layout>
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Category Details</h2>
                    <a href="{{ route('categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Back to Categories
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">ID</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $category->id }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Name</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $category->name }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Description</h3>
                            <p class="mt-1 text-gray-900">{{ $category->description ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <div class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $category->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Business ID</h3>
                            <p class="mt-1 text-gray-900">{{ $category->business_id }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Number of Products</h3>
                            <p class="mt-1 text-gray-900">{{ $category->products->count() }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                                <p class="mt-1 text-gray-900">{{ $category->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Updated At</h3>
                                <p class="mt-1 text-gray-900">{{ $category->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex space-x-3">
                    <a href="{{ route('categories.edit', $category->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        Edit
                    </a>
                    
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone if there are associated products.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 {{ $category->products->count() > 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $category->products->count() > 0 ? 'disabled' : '' }}>
                            Delete
                        </button>
                    </form>
                    
                    <a href="{{ route('categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Back to Categories
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>