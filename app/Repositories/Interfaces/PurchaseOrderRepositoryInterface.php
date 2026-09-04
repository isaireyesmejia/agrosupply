<?php

namespace App\Repositories\Interfaces;

use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Collection;

interface PurchaseOrderRepositoryInterface
{
    public function getAll(): Collection;

    public function find(int $id): ?PurchaseOrder;

    public function create(array $data): PurchaseOrder;

    public function update(PurchaseOrder $order, array $data): PurchaseOrder;

    public function delete(PurchaseOrder $order): bool;
}