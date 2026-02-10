<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
                    <h2 class="text-2xl font-semibold text-gray-800">Categories</h2>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                            Add New Category
                        </a>
                        <a href="{{ route('categories.export.csv') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                            Export to CSV
                        </a>
                        <a href="{{ route('categories.export.pdf') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                            Export to PDF
                        </a>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form method="GET" action="{{ route('categories.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search categories..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Statuses</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 text-white py-2 px-4 rounded-md transition duration-300 ease-in-out">
                                Apply Filters
                            </button>
                            <a href="{{ route('categories.index') }}" class="ml-2 w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md transition duration-300 ease-in-out text-center">
                                Clear
                            </a>
                        </div>
                    </form>
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

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('categories.index', array_merge(request()->query(), ['sort_by' => 'id', 'sort_order' => request('sort_by') == 'id' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                                        ID
                                        @if(request('sort_by') == 'id')
                                            <span>{{ request('sort_order') == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('categories.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_order' => request('sort_by') == 'name' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                                        Name
                                        @if(request('sort_by') == 'name')
                                            <span>{{ request('sort_order') == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('categories.index', array_merge(request()->query(), ['sort_by' => 'status', 'sort_order' => request('sort_by') == 'status' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                                        Status
                                        @if(request('sort_by') == 'status')
                                            <span>{{ request('sort_order') == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('categories.index', array_merge(request()->query(), ['sort_by' => 'business_id', 'sort_order' => request('sort_by') == 'business_id' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                                        Business ID
                                        @if(request('sort_by') == 'business_id')
                                            <span>{{ request('sort_order') == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('categories.index', array_merge(request()->query(), ['sort_by' => 'products_count', 'sort_order' => request('sort_by') == 'products_count' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                                        Product Count
                                        @if(request('sort_by') == 'products_count')
                                            <span>{{ request('sort_order') == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($categories as $category)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $category->description }}">{{ Str::limit($category->description, 50) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $category->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->business_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->products->count() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('categories.show', $category->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('categories.edit', $category->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone if there are associated products.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 {{ $category->products->count() > 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $category->products->count() > 0 ? 'disabled' : '' }}>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>