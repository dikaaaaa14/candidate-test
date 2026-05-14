<x-app-layout>
    <x-slot name="header">
        <div class="text-sm text-gray-500 mb-1">
            <a href="{{ route('suppliers.index') }}" class="hover:text-primary-600">Suppliers</a>
            <span class="mx-1">/</span>
            <span class="text-gray-700">{{ $supplier->name }}</span>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <!-- Supplier Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $supplier->name }}</h2>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Active Partner</span>
                </div>
                <p class="text-sm text-gray-400 mt-1">ID: SUP-{{ str_pad($supplier->id, 7, '0', STR_PAD_LEFT) }}</p>
            </div>
            <a href="{{ route('suppliers.edit', $supplier) }}"
               class="inline-flex items-center gap-2 text-sm border border-gray-200 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Edit Supplier
            </a>
        </div>
    </div>

    <!-- Layups Section -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-gray-900">Associated Layups</h3>
            <div class="flex gap-2">
                <a href="{{ route('suppliers.import.form', $supplier) }}"
                   class="inline-flex items-center gap-2 text-sm border border-gray-200 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import
                </a>
                <a href="{{ route('suppliers.export', $supplier) }}"
                   class="inline-flex items-center gap-2 text-sm border border-gray-200 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </a>
                <a href="{{ route('suppliers.layups.create', $supplier) }}"
                   class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm px-3 py-2 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Layup
                </a>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layup ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layers</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($supplier->layups as $layup)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-gray-500 text-xs font-mono">L-{{ str_pad($layup->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $layup->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $layup->layers->count() }} layers</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('suppliers.layups.layers.create', [$supplier, $layup]) }}" class="text-primary-600 hover:underline text-xs">+ Layer</a>
                            <a href="{{ route('suppliers.layups.edit', [$supplier, $layup]) }}" class="text-gray-500 hover:underline text-xs">Edit</a>
                            <form method="POST" action="{{ route('suppliers.layups.destroy', [$supplier, $layup]) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No layups yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-3 border-t border-gray-100 text-xs text-gray-500">
            Showing {{ $supplier->layups->count() }} layups
        </div>
    </div>
</x-app-layout>