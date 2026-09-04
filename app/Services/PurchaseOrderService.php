<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderService
{
    public function __construct(
        protected PurchaseOrderRepositoryInterface $repository
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }

    public function find(int $id): ?PurchaseOrder
    {
        return $this->repository->find($id);
    }

    /**
     * Crea la orden con sus items dentro de una transacción,
     * calculando subtotales y el total general.
     *
     * $items esperado: [['product_id' => 1, 'quantity' => 5, 'unit_price' => 20.00], ...]
     */
    public function create(array $data, array $items): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $items) {
            $data['tenant_id'] = Auth::user()->tenant_id;
            $data['user_id'] = Auth::id();
            $data['status'] = 'pendiente';

            $total = 0;
            $preparedItems = [];

            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $total += $subtotal;

                $preparedItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ];
            }

            $data['total'] = $total;

            $order = $this->repository->create($data);
            $order->items()->createMany($preparedItems);

            return $order->load('items.product', 'supplier');
        });
    }

    public function approve(PurchaseOrder $order): PurchaseOrder
    {
        $this->assertStatus($order, 'pendiente', 'aprobar');

        $order->update([
            'status' => 'aprobada',
            'approved_at' => now(),
        ]);

        return $order;
    }

    public function reject(PurchaseOrder $order): PurchaseOrder
    {
        $this->assertStatus($order, 'pendiente', 'rechazar');

        $order->update(['status' => 'rechazada']);

        return $order;
    }

    public function cancel(PurchaseOrder $order): PurchaseOrder
    {
        if (! in_array($order->status, ['pendiente', 'aprobada'])) {
            throw ValidationException::withMessages([
                'status' => 'Solo se pueden cancelar órdenes pendientes o aprobadas.',
            ]);
        }

        $order->update(['status' => 'cancelada']);

        return $order;
    }

    /**
     * Marca la orden como recibida y suma el stock de cada
     * producto, todo dentro de una transacción.
     */
    public function receive(PurchaseOrder $order): PurchaseOrder
    {
        $this->assertStatus($order, 'aprobada', 'recibir');

        return DB::transaction(function () use ($order) {
            $order->load('items');

            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)
                    ->increment('stock', $item->quantity);
            }

            $order->update([
                'status' => 'recibida',
                'received_at' => now(),
            ]);

            return $order;
        });
    }

    protected function assertStatus(PurchaseOrder $order, string $expected, string $action): void
    {
        if ($order->status !== $expected) {
            throw ValidationException::withMessages([
                'status' => "No se puede {$action} una orden en estado \"{$order->status}\".",
            ]);
        }
    }
}