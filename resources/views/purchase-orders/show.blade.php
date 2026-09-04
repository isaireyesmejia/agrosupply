<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Orden de compra #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Proveedor</p>
                        <p class="font-medium">{{ $order->supplier->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Solicitó</p>
                        <p class="font-medium">{{ $order->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Estado</p>
                        <p class="font-medium">{{ ucfirst($order->status) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total</p>
                        <p class="font-medium">${{ number_format($order->total, 2) }}</p>
                    </div>
                    @if ($order->notes)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Notas</p>
                            <p>{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>

                <h3 class="text-lg font-medium mb-2">Productos</h3>
                <table class="min-w-full divide-y divide-gray-200 mb-6">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio unit.</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-4 py-2">{{ $item->product->name }}</td>
                                <td class="px-4 py-2">{{ $item->quantity }}</td>
                                <td class="px-4 py-2">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-2">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                                <div class="flex space-x-2">
                    @can('approve', $order)
                        <form action="{{ route('purchase-orders.approve', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Aprobar
                            </button>
                        </form>
                    @endcan

                    @can('reject', $order)
                        <form action="{{ route('purchase-orders.reject', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                Rechazar
                            </button>
                        </form>
                    @endcan

                    @can('receive', $order)
                        <form action="{{ route('purchase-orders.receive', $order) }}" method="POST"
                              onsubmit="return confirm('Esto sumará el stock de los productos. ¿Confirmar recepción?');">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Marcar como recibida
                            </button>
                        </form>
                    @endcan

                    @can('cancel', $order)
                        <form action="{{ route('purchase-orders.cancel', $order) }}" method="POST"
                              onsubmit="return confirm('¿Cancelar esta orden?');">
                            @csrf
                            <button type="submit" class="border border-gray-300 px-4 py-2 rounded hover:bg-gray-50">
                                Cancelar
                            </button>
                        </form>
                    @endcan
                </div>

                <div class="mt-6">
                    <a href="{{ route('purchase-orders.index') }}" class="text-blue-600 hover:underline">
                        ← Volver al listado
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>