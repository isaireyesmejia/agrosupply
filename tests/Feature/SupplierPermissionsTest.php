<?php

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->tenant = Tenant::factory()->create();
});

it('allows Administrador to view and create suppliers', function () {
    $admin = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $admin->assignRole('Administrador');

    $this->actingAs($admin);

    $this->get('/suppliers')->assertOk();
    $this->get('/suppliers/create')->assertOk();
});

it('allows Comprador to view suppliers but blocks creating one', function () {
    $comprador = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $comprador->assignRole('Comprador');

    $this->actingAs($comprador);

    $this->get('/suppliers')->assertOk();
    $this->get('/suppliers/create')->assertForbidden();
});

it('allows Proveedor to view suppliers but blocks creating one', function () {
    $proveedor = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $proveedor->assignRole('Proveedor');

    $this->actingAs($proveedor);

    $this->get('/suppliers')->assertOk();
    $this->get('/suppliers/create')->assertForbidden();
});