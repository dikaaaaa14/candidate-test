<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <!-- Logo + Nav Links -->
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </div>
                    <span class="font-semibold text-gray-800 text-sm">CLT Layup <span class="text-gray-400 font-normal">MANAGER</span></span>
                </div>

                <div class="flex items-center gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        class="text-sm px-3 py-2 rounded hover:bg-gray-100 text-gray-600">
                        Overview
                    </x-nav-link>
                    <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')"
                        class="text-sm px-3 py-2 rounded hover:bg-gray-100 text-gray-600">
                        Suppliers
                    </x-nav-link>
                    <x-nav-link href="#" class="text-sm px-3 py-2 rounded hover:bg-gray-100 text-gray-600">
                        Layups
                    </x-nav-link>
                    <x-nav-link href="#" class="text-sm px-3 py-2 rounded hover:bg-gray-100 text-gray-600">
                        Layers
                    </x-nav-link>
                    <x-nav-link href="#" class="text-sm px-3 py-2 rounded hover:bg-gray-100 text-gray-600">
                        Settings
                    </x-nav-link>
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center gap-3">
                <button class="p-2 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-xs font-semibold text-gray-600">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="text-sm">
                        <div class="font-medium text-gray-700">{{ Auth::user()->name }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-gray-400 hover:text-gray-600">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>