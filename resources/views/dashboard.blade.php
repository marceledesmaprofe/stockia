<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                        <p class="mt-2 text-gray-600">Welcome to your inventory management system</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Products -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ $totalProducts }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Categories -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total Categories</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ $totalCategories }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Products -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Active Products</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ $activeProducts }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Low Stock Products -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Low Stock Items</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ $lowStockProducts }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Recent Activity -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Top Categories -->
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Top Categories</h3>
                                <p class="mt-1 text-sm text-gray-500">Categories with most products</p>
                            </div>
                            <div class="px-4 py-5 sm:p-6">
                                @if($topCategories->count() > 0)
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($topCategories as $category)
                                            <li class="py-4">
                                                <div class="flex items-center">
                                                    <div class="min-w-0 flex-1 flex items-center">
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $category->name }}</p>
                                                            <p class="text-sm text-gray-500 truncate">{{ $category->products_count }} products</p>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm text-gray-900 font-medium">{{ $category->products_count }}</div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500 italic">No categories found.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Recent Products -->
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Products</h3>
                                <p class="mt-1 text-sm text-gray-500">Latest additions to your inventory</p>
                            </div>
                            <div class="px-4 py-5 sm:p-6">
                                @if($recentProducts->count() > 0)
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($recentProducts as $product)
                                            <li class="py-4">
                                                <div class="flex items-center">
                                                    <div class="min-w-0 flex-1 flex items-center">
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                                            <p class="text-sm text-gray-500 truncate">
                                                                @if($product->category)
                                                                    Category: {{ $product->category->name }}
                                                                @else
                                                                    Uncategorized
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4 flex-shrink-0">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $product->status ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $product->created_at->diffForHumans() }}
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500 italic">No products found.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>