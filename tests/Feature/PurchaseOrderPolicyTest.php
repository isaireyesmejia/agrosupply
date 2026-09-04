<?php

use App\Models\PurchaseOrder;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->tenant = Tenant::factory()->create();
});

it('allows Administrador to approve a pendiente order', function () {
    $admin = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $admin->assignRole('Administrador');

    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'pendiente',
    ]);

    expect($admin->can('approve', $order))->toBeTrue();
});

it('blocks approving an order that is not pendiente', function () {
    $admin = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $admin->assignRole('Administrador');

    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'aprobada',
    ]);

    expect($admin->can('approve', $order))->toBeFalse();
});

it('blocks Comprador from approving an order', function () {
    $comprador = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $comprador->assignRole('Comprador');

    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'pendiente',
    ]);

    expect($comprador->can('approve', $order))->toBeFalse();
});

it('allows Comprador to cancel their own pendiente order', function () {
    $comprador = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $comprador->assignRole('Comprador');

    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'user_id' => $comprador->id,
        'status' => 'pendiente',
    ]);

    expect($comprador->can('cancel', $order))->toBeTrue();
});

it('blocks Comprador from cancelling another Comprador order', function () {
    $compradorUno = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $compradorUno->assignRole('Comprador');

    $compradorDos = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $compradorDos->assignRole('Comprador');

    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'user_id' => $compradorUno->id,
        'status' => 'pendiente',
    ]);

    expect($compradorDos->can('cancel', $order))->toBeFalse();
});

it('allows Administrador to cancel any order regardless of owner', function () {
    $comprador = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $comprador->assignRole('Comprador');

    $admin = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $admin->assignRole('Administrador');

    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'user_id' => $comprador->id,
        'status' => 'pendiente',
    ]);

    expect($admin->can('cancel', $order))->toBeTrue();
});

it('blocks cancelling an order that is already recibida', function () {
    $admin = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $admin->assignRole('Administrador');

    $order = PurchaseOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'recibida',
    ]);

    expect($admin->can('cancel', $order))->toBeFalse();
});