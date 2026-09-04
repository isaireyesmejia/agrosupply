<?php

use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->tenant = Tenant::factory()->create();
});

it('allows Administrador to view and create products', function () {
    $admin = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $admin->assignRole('Administrador');

    $this->actingAs($admin);

    $this->get('/products')->assertOk();
    $this->get('/products/create')->assertOk();
});

it('allows Comprador to view products but blocks creating one', function () {
    $comprador = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $comprador->assignRole('Comprador');

    $this->actingAs($comprador);

    $this->get('/products')->assertOk();
    $this->get('/products/create')->assertForbidden();
});

it('allows Proveedor to view products but blocks creating one', function () {
    $proveedor = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $proveedor->assignRole('Proveedor');

    $this->actingAs($proveedor);

    $this->get('/products')->assertOk();
    $this->get('/products/create')->assertForbidden();
});

it('blocks a user with no role from viewing products', function () {
    $userSinRol = User::factory()->create(['tenant_id' => $this->tenant->id]);

    $this->actingAs($userSinRol);

    $this->get('/products')->assertForbidden();
});