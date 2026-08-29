<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Productos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @can('products.manage')
    <a href="{{ route('products.create') }}" class="inline-block mb-4 px-4 py-2 bg-gray-800 text-white rounded">
        Nuevo Producto
    </a>
@endcan

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">SKU</th>
                            <th class="py-2">Nombre</th>
                            <th class="py-2">Precio</th>
                            <th class="py-2">Stock</th>
                            <th class="py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="border-b">
                                <td class="py-2">{{ $product->sku }}</td>
                                <td class="py-2">{{ $product->name }}</td>
                                <td class="py-2">${{ number_format($product->price, 2) }}</td>
                                <td class="py-2">{{ $product->stock }}</td>
                                <td class="py-2 space-x-2">
    @can('products.manage')
        <a href="{{ route('products.edit', $product->id) }}" class="text-blue-600">Editar</a>
        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este producto?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600">Eliminar</button>
        </form>
    @endcan
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-gray-500">No hay productos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>