<?php

use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('only returns products belonging to the authenticated user tenant', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $userA = User::factory()->create(['tenant_id' => $tenantA->id]);

    Product::factory()->count(3)->create(['tenant_id' => $tenantA->id]);
    Product::factory()->count(2)->create(['tenant_id' => $tenantB->id]);

    $this->actingAs($userA);

    $products = Product::all();

    expect($products)->toHaveCount(3);
    expect($products->pluck('tenant_id')->unique()->values()->all())
        ->toBe([$tenantA->id]);
});

it('prevents accessing a product record from another tenant directly', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $userA = User::factory()->create(['tenant_id' => $tenantA->id]);
    $productFromB = Product::factory()->create(['tenant_id' => $tenantB->id]);

    $this->actingAs($userA);

    $found = Product::find($productFromB->id);

    expect($found)->toBeNull();
});