<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Órdenes de compra
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

                @if (session('errors'))
                    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                        {{ session('errors')->first() }}
                    </div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Listado de órdenes</h3>

                    @can('purchase-orders.create')
                        <a href="{{ route('purchase-orders.create') }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Nueva orden
                        </a>
                    @endcan
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Proveedor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Solicitó</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-4 py-2">#{{ $order->id }}</td>
                                <td class="px-4 py-2">{{ $order->supplier->name }}</td>
                                <td class="px-4 py-2">{{ $order->user->name }}</td>
                                <td class="px-4 py-2">${{ number_format($order->total, 2) }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $colors = [
                                            'pendiente' => 'bg-yellow-100 text-yellow-800',
                                            'aprobada' => 'bg-blue-100 text-blue-800',
                                            'recibida' => 'bg-green-100 text-green-800',
                                            'rechazada' => 'bg-red-100 text-red-800',
                                            'cancelada' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('purchase-orders.show', $order) }}"
                                       class="text-blue-600 hover:underline">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                    No hay órdenes de compra registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>