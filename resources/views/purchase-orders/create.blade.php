<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nueva orden de compra
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('purchase-orders.store') }}" method="POST"
                      x-data="purchaseOrderForm()">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Proveedor *</label>
                        <select name="supplier_id" class="mt-1 block w-full rounded border-gray-300">
                            <option value="">Selecciona un proveedor</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Notas</label>
                        <textarea name="notes" rows="2" class="mt-1 block w-full rounded border-gray-300">{{ old('notes') }}</textarea>
                    </div>

                    <h3 class="text-lg font-medium mb-2 mt-6">Productos</h3>

                    <table class="min-w-full mb-2">
                        <thead>
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Cantidad</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Precio unit.</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Subtotal</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-2 py-1">
                                        <select :name="`items[${index}][product_id]`"
                                                x-model="item.product_id"
                                                @change="updatePrice(index)"
                                                class="block w-full rounded border-gray-300">
                                            <option value="">Selecciona producto</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-2 py-1">
                                        <input type="number" min="1" :name="`items[${index}][quantity]`"
                                               x-model.number="item.quantity"
                                               class="block w-full rounded border-gray-300">
                                    </td>
                                    <td class="px-2 py-1">
                                        <input type="number" min="0" step="0.01" :name="`items[${index}][unit_price]`"
                                               x-model.number="item.unit_price"
                                               class="block w-full rounded border-gray-300">
                                    </td>
                                    <td class="px-2 py-1 text-right" x-text="'$' + (item.quantity * item.unit_price).toFixed(2)"></td>
                                    <td class="px-2 py-1 text-right">
                                        <button type="button" @click="removeItem(index)" class="text-red-600">✕</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <button type="button" @click="addItem()"
                            class="text-blue-600 hover:underline text-sm mb-4">
                        + Agregar producto
                    </button>

                    <div class="text-right text-lg font-medium mb-6">
                        Total: $<span x-text="total().toFixed(2)"></span>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('purchase-orders.index') }}"
                           class="px-4 py-2 rounded border border-gray-300">Cancelar</a>
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Crear orden
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function purchaseOrderForm() {
            return {
                items: [
                    { product_id: '', quantity: 1, unit_price: 0 }
                ],
                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_price: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                updatePrice(index) {
                    const select = event.target;
                    const selectedOption = select.options[select.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    this.items[index].unit_price = price ? parseFloat(price) : 0;
                },
                total() {
                    return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
                }
            }
        }
    </script>
</x-app-layout>