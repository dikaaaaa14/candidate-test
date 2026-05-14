<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Suppliers</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage timber suppliers and material sourcing.</p>
            </div>
            <a href="{{ route('suppliers.create') }}"
               class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Supplier
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('suppliers.index') }}" class="flex items-center gap-3 mb-4" id="filterForm">
    <div class="relative flex-1 max-w-xs">
        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
        </svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search suppliers by name..."
               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
               oninput="this.form.submit()">
    </div>

    <!-- Filter dropdown -->
    <div class="relative" x-data="{ open: false }">
        <button type="button" @click="open = !open"
                class="inline-flex items-center gap-2 text-sm border border-gray-200 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter
        </button>
        <div x-show="open" @click.outside="open = false"
             class="absolute left-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4 z-10 min-w-[220px]">
            <div class="mb-2">
                <label class="text-xs text-gray-500">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full text-sm border border-gray-200 rounded px-2 py-1 mt-0.5">
            </div>
            <div class="mb-3">
                <label class="text-xs text-gray-500">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full text-sm border border-gray-200 rounded px-2 py-1 mt-0.5">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 text-xs bg-primary-600 text-white px-2 py-1.5 rounded">Apply</button>
                <a href="{{ route('suppliers.index') }}" class="flex-1 text-xs text-center border border-gray-200 px-2 py-1.5 rounded text-gray-600">Reset</a>
            </div>
        </div>
    </div>

    <!-- Export button -->
    <a href="{{ route('suppliers.index', array_merge(request()->query(), ['export' => 1])) }}"
       class="inline-flex items-center gap-2 text-sm border border-gray-200 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Export CSV
    </a>
</form>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Layups</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($suppliers as $supplier)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                 style="background-color: {{ ['#4a7c3f','#3b82f6','#8b5cf6','#f59e0b','#ef4444'][$loop->index % 5] }}">
                                {{ strtoupper(substr($supplier->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $supplier->name }}</div>
                                <div class="text-xs text-gray-400">ID: SUP-{{ str_pad($supplier->id, 7, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $supplier->layups_count }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $supplier->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="text-primary-600 hover:underline font-medium">View</a>
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="text-gray-500 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No suppliers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-3 border-t border-gray-100 text-xs text-gray-500">
            Showing {{ $suppliers->count() }} results
        </div>
    </div>
</x-app-layout>