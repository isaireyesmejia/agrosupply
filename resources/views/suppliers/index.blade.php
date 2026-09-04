<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Proveedores
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Listado de proveedores</h3>
                    @can('suppliers.manage')
                        <a href="{{ route('suppliers.create') }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Nuevo proveedor
                        </a>
                    @endcan
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            @can('suppliers.manage')
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td class="px-4 py-2">{{ $supplier->name }}</td>
                                <td class="px-4 py-2">{{ $supplier->rfc ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $supplier->contact_name ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $supplier->phone ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $supplier->email ?? '—' }}</td>
                                @can('suppliers.manage')
                                    <td class="px-4 py-2 text-right space-x-2">
                                        <a href="{{ route('suppliers.edit', $supplier) }}"
                                           class="text-blue-600 hover:underline">Editar</a>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('¿Eliminar este proveedor?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                    No hay proveedores registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>