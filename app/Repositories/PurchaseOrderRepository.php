<?php

namespace App\Repositories;

use App\Models\PurchaseOrder;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PurchaseOrderRepository implements PurchaseOrderRepositoryInterface
{
    public function getAll(): Collection
    {
        return PurchaseOrder::with(['supplier', 'user', 'items.product'])
            ->latest()
            ->get();
    }

    public function find(int $id): ?PurchaseOrder
    {
        return PurchaseOrder::with(['supplier', 'user', 'items.product'])->find($id);
    }

    public function create(array $data): PurchaseOrder
    {
        return PurchaseOrder::create($data);
    }

    public function update(PurchaseOrder $order, array $data): PurchaseOrder
    {
        $order->update($data);

        return $order;
    }

    public function delete(PurchaseOrder $order): bool
    {
        return $order->delete();
    }
}