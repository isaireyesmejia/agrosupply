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
    }
}