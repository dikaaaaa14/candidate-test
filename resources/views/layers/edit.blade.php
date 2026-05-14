<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Edit Layer</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
                <form method="POST" action="{{ route('suppliers.layups.layers.update', [$supplier, $layup, $layer]) }}">
                    @csrf @method('PUT')
                    <x-input-label for="layer_order" value="Layer Order" />
                    <x-text-input id="layer_order" name="layer_order" type="number" class="mt-1 block w-full" value="{{ $layer->layer_order }}" required />

                    <x-input-label for="thickness" value="Thickness" class="mt-3" />
                    <x-text-input id="thickness" name="thickness" type="number" step="0.01" class="mt-1 block w-full" value="{{ $layer->thickness }}" required />

                    <x-input-label for="width" value="Width" class="mt-3" />
                    <x-text-input id="width" name="width" type="number" step="0.01" class="mt-1 block w-full" value="{{ $layer->width }}" required />

                    <x-input-label for="angle" value="Angle" class="mt-3" />
                    <x-text-input id="angle" name="angle" type="number" step="0.01" class="mt-1 block w-full" value="{{ $layer->angle }}" required />

                    <div class="mt-4 flex gap-2">
                        <x-primary-button>Update</x-primary-button>
                        <a href="{{ route('suppliers.show', $supplier) }}" class="text-gray-500 mt-1">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>