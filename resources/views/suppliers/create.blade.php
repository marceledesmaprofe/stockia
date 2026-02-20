<x-app-layout>
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Add New Supplier</h2>

                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="document_type" class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('document_type') border-red-500 @enderror" id="document_type" name="document_type">
                                <option value="">Select...</option>
                                <option value="DNI" {{ old('document_type') == 'DNI' ? 'selected' : '' }}>DNI</option>
                                <option value="RUC" {{ old('document_type') == 'RUC' ? 'selected' : '' }}>RUC</option>
                                <option value="CUIT" {{ old('document_type') == 'CUIT' ? 'selected' : '' }}>CUIT</option>
                                <option value="PASAPORTE" {{ old('document_type') == 'PASAPORTE' ? 'selected' : '' }}>Pasaporte</option>
                                <option value="OTRO" {{ old('document_type') == 'OTRO' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('document_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="document_number" class="block text-sm font-medium text-gray-700 mb-1">Document Number</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('document_number') border-red-500 @enderror" id="document_number" name="document_number" value="{{ old('document_number') }}">
                            @error('document_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('full_name') border-red-500 @enderror" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                        @error('full_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" id="status" name="status" required>
                            <option value="1" {{ old('status') === 0 || old('status') === '0' ? '' : 'selected' }}>Active</option>
                            <option value="0" {{ old('status') === 0 || old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mt-6 space-x-3">
                        <a href="{{ route('suppliers.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Create Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
