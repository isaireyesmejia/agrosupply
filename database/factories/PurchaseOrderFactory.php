<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'supplier_id' => Supplier::factory(),
            'status' => 'pendiente',
            'total' => $this->faker->randomFloat(2, 100, 5000),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}