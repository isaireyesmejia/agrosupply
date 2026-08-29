<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $repository
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getById(int $id): ?Product
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Product
    {
        $data['tenant_id'] = auth()->user()->tenant_id;
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Product
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}