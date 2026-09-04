<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SupplierService
{
    public function __construct(
        protected SupplierRepositoryInterface $repository
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }

    public function find(int $id): ?Supplier
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Supplier
    {
        $data['tenant_id'] = Auth::user()->tenant_id;

        return $this->repository->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        return $this->repository->update($supplier, $data);
    }

    public function delete(Supplier $supplier): bool
    {
        return $this->repository->delete($supplier);
    }
}