<?php

namespace App\Repositories\Interfaces;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;

interface SupplierRepositoryInterface
{
    public function getAll(): Collection;

    public function find(int $id): ?Supplier;

    public function create(array $data): Supplier;

    public function update(Supplier $supplier, array $data): Supplier;

    public function delete(Supplier $supplier): bool;
}