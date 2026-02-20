<x-app-layout>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
                    <h2 class="text-2xl font-semibold text-gray-800">Suppliers</h2>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('suppliers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                            Add New Supplier
                        </a>
                        <a href="{{ route('suppliers.export.csv') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                            Export to CSV
                        </a>
                        <a href="{{ route('suppliers.export.pdf') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                            Export to PDF
                        </a>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form method="GET" action="{{ route('suppliers.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search suppliers..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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
                            <a href="{{ route('suppliers.index') }}" class="ml-2 w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md transition duration-300 ease-in-out text-center">
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
                                    <a href="{{ route('suppliers.index', array_merge(request()->query(), ['sort_by' => 'id', 'sort_order' => request('sort_by') == 'id' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                                        ID
                                        @if(request('sort_by') == 'id')
                                            <span>{{ request('sort_order') == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('suppliers.index', array_merge(request()->query(), ['sort_by' => 'full_name', 'sort_order' => request('sort_by') == 'full_name' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                                        Name
                                        @if(request('sort_by') == 'full_name')
                                            <span>{{ request('sort_order') == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('suppliers.index', array_merge(request()->query(), ['sort_by' => 'status', 'sort_order' => request('sort_by') == 'status' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                                        Status
                                        @if(request('sort_by') == 'status')
                                            <span>{{ request('sort_order') == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($suppliers as $supplier)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supplier->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($supplier->document_type && $supplier->document_number)
                                            <span class="font-semibold">{{ $supplier->document_type }}:</span> {{ $supplier->document_number }}
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $supplier->full_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supplier->phone ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supplier->email ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $supplier->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $supplier->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('suppliers.show', $supplier->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('suppliers.edit', $supplier->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                            <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No suppliers found.</td>
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
