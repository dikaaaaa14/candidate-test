<x-app-layout>
    <x-slot name="header">
        <div class="text-sm text-gray-500 mb-1">
            <a href="{{ route('suppliers.index') }}" class="hover:text-primary-600">Suppliers</a>
            <span class="mx-1">/</span>
            <a href="{{ route('suppliers.show', $supplier) }}" class="hover:text-primary-600">{{ $supplier->name }}</a>
            <span class="mx-1">/</span>
            <span class="text-gray-700">Import</span>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Import Layup Data</h2>
            <p class="text-sm text-gray-500 mb-6">Upload a JSON file exported from this system.</p>

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('suppliers.import', $supplier) }}" enctype="multipart/form-data">
                @csrf

                <!-- Upload Area -->
                <div class="border-2 border-dashed border-gray-200 rounded-lg p-8 text-center mb-4 hover:border-primary-400 transition-colors">
                    <svg class="w-8 h-8 text-primary-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <label class="cursor-pointer">
                        <span class="text-primary-600 font-medium">Click to upload</span>
                        <span class="text-gray-500"> or drag and drop</span>
                        <input type="file" name="file" accept=".json" class="hidden" required>
                    </label>
                    <p class="text-xs text-gray-400 mt-1">JSON up to 10MB</p>
                </div>

                <!-- Strategy -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conflict Resolution Strategy</label>
                    <select name="strategy" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="skip">Skip conflicts (Default)</option>
                        <option value="overwrite">Overwrite existing</option>
                        <option value="duplicate">Duplicate layup</option>
                    </select>
                </div>

                <div class="flex gap-3 justify-end">
                    <a href="{{ route('suppliers.show', $supplier) }}"
                       class="px-4 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm px-4 py-2 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Confirm Import
                        <input type="file" name="file" accept=".csv" required>
                        <select name="strategy">
                        <option value="skip">Skip conflicts</option>
                        <option value="overwrite">Overwrite</option>
                        <option value="duplicate">Duplicate</option>
                        </select>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>