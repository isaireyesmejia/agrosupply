<?php

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->actingAs($this->user);

    $this->service = app(PurchaseOrderService::class);
});

it('calculates subtotals and total correctly when creating an order', function () {
    $supplier = Supplier::factory()->create(['tenant_id' => $this->tenant->id]);
    $productA = Product::factory()->create(['tenant_id' => $this->tenant->id]);
    $productB = Product::factory()->create(['tenant_id' => $this->tenant->id]);

    $order = $this->service->create(
        ['supplier_id' => $supplier->id],
        [
            ['product_id' => $productA->id, 'quantity' => 3, 'unit_price' => 10.00],
            ['product_id' => $productB->id, 'quantity' => 2, 'unit_price' => 25.00],
        ]
    );

    expect($order->status)->toBe('pendiente');
    expect($order->tenant_id)->toBe($this->tenant->id);
    expect($order->user_id)->toBe($this->user->id);
    expect((float) $order->total)->toBe(80.00); // (3*10) + (2*25)
    expect($order->items)->toHaveCount(2);
    expect((float) $order->items[0]->subtotal)->toBe(30.00);
});

it('approves a pendiente order and sets approved_at', function () {
    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'pendiente',
    ]);

    $approved = $this->service->approve($order);

    expect($approved->status)->toBe('aprobada');
    expect($approved->approved_at)->not->toBeNull();
});

it('throws when approving an order that is not pendiente', function () {
    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'aprobada',
    ]);

    $this->service->approve($order);
})->throws(ValidationException::class);

it('rejects a pendiente order', function () {
    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'pendiente',
    ]);

    $rejected = $this->service->reject($order);

    expect($rejected->status)->toBe('rechazada');
});

it('cancels a pendiente or aprobada order', function () {
    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'aprobada',
    ]);

    $cancelled = $this->service->cancel($order);

    expect($cancelled->status)->toBe('cancelada');
});

it('throws when cancelling an order that is already recibida', function () {
    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'recibida',
    ]);

    $this->service->cancel($order);
})->throws(ValidationException::class);

it('increments product stock for each item when receiving an order', function () {
    $productA = Product::factory()->create(['tenant_id' => $this->tenant->id, 'stock' => 10]);
    $productB = Product::factory()->create(['tenant_id' => $this->tenant->id, 'stock' => 5]);

    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'aprobada',
    ]);

    $order->items()->createMany([
        ['product_id' => $productA->id, 'quantity' => 4, 'unit_price' => 10, 'subtotal' => 40],
        ['product_id' => $productB->id, 'quantity' => 7, 'unit_price' => 5, 'subtotal' => 35],
    ]);

    $received = $this->service->receive($order);

    expect($received->status)->toBe('recibida');
    expect($received->received_at)->not->toBeNull();
    expect($productA->fresh()->stock)->toBe(14); // 10 + 4
    expect($productB->fresh()->stock)->toBe(12); // 5 + 7
});

it('throws when receiving an order that is not aprobada', function () {
    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'pendiente',
    ]);

    $this->service->receive($order);
})->throws(ValidationException::class);

it('cannot receive the same order twice', function () {
    $product = Product::factory()->create(['tenant_id' => $this->tenant->id, 'stock' => 10]);

    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'aprobada',
    ]);

    $order->items()->create([
        'product_id' => $product->id, 'quantity' => 5, 'unit_price' => 10, 'subtotal' => 50,
    ]);

    $this->service->receive($order);

    expect(fn () => $this->service->receive($order->fresh()))
        ->toThrow(ValidationException::class);

    expect($product->fresh()->stock)->toBe(15); // solo se sumó una vez
});