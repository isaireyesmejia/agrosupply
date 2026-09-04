<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Administrador', 'Comprador', 'Proveedor'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        foreach (['products.view', 'products.manage'] as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::findByName('Administrador');
        $admin->givePermissionTo(['products.view', 'products.manage']);

        $comprador = Role::findByName('Comprador');
        $comprador->givePermissionTo('products.view');

        $proveedor = Role::findByName('Proveedor');
        $proveedor->givePermissionTo('products.view');

        // Permisos de Supplier
        $suppliersView = Permission::firstOrCreate(['name' => 'suppliers.view']);
        $suppliersManage = Permission::firstOrCreate(['name' => 'suppliers.manage']);

        $admin->givePermissionTo([$suppliersView, $suppliersManage]);
        $comprador->givePermissionTo([$suppliersView]);
        $proveedor->givePermissionTo([$suppliersView]);

        // Permisos de Órdenes de Compra
        $poView = Permission::firstOrCreate(['name' => 'purchase-orders.view']);
        $poCreate = Permission::firstOrCreate(['name' => 'purchase-orders.create']);
        $poApprove = Permission::firstOrCreate(['name' => 'purchase-orders.approve']);
        $poReceive = Permission::firstOrCreate(['name' => 'purchase-orders.receive']);
        $poCancel = Permission::firstOrCreate(['name' => 'purchase-orders.cancel']);

        // Administrador: todo
        $admin->givePermissionTo([$poView, $poCreate, $poApprove, $poReceive, $poCancel]);

        // Comprador: puede ver, crear y cancelar sus propias órdenes, pero no aprobar ni recibir
        $comprador->givePermissionTo([$poView, $poCreate, $poCancel]);

    }
}