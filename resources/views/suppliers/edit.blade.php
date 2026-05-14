<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Edit Supplier</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                    @csrf @method('PUT')
                    <x-input-label for="name" value="Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ $supplier->name }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    <div class="mt-4 flex gap-2">
                        <x-primary-button>Update</x-primary-button>
                        <a href="{{ route('suppliers.index') }}" class="text-gray-500 mt-1">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>