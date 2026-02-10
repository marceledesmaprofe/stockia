<x-guest-layout>
    <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#ff2a4f] selection:text-white">
        <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
            <main class="mt-6">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to {{ config('app.name', 'Laravel') }}</h1>
                    <p class="text-lg text-gray-600 mb-8">Manage your inventory efficiently with our system</p>
                    
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Login
                        </a>
                        
                        <a href="{{ route('register') }}" class="px-6 py-3 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Register
                        </a>
                    </div>
                </div>
            </main>

            <footer class="py-16 text-center text-sm text-gray-700">
                © {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
            </footer>
        </div>
    </div>
</x-guest-layout>