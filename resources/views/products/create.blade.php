<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuevo Producto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('products.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Nombre</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('sku') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Descripción</label>
                        <textarea name="description" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Precio</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('price') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Stock</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('stock') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded">Guardar</button>
                        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancelar</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>