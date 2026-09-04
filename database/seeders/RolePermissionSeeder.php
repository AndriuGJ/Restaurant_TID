<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Permisos por módulo
        $permissions = [
            // Configuración
            'configuracion-ver',
            'configuracion-editar',

            // POS
            'pos-ver',
            'pos-ventas',
            'pos-preparacion',
            'pos-cobro',
            'pos-delivery',
            'pos-venta-rapida',

            // Clientes
            'clientes-ver',
            'clientes-gestionar',

            // Cajas
            'cajas-abrir',
            'cajas-cerrar',
            'cajas-ver',

            // Inventario
            'inventario-ver',
            'compras-gestionar',
            'productos-gestionar',
            'kardex-ver',

            // Reportes
            'reportes-ver',
            'reportes-cajas-ver',

            // Usuarios
            'usuarios-ver',
            'usuarios-gestionar',
            'roles-gestionar',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Roles
        $roles = [
            'administrador' => $permissions,
            'chef' => [
                'pos-ver',
                'pos-preparacion',
            ],
            'mozo' => [
                'pos-ver',
                'pos-ventas',
                'pos-cobro',
                'pos-delivery',
                'pos-venta-rapida',
                'clientes-ver',
                'clientes-gestionar',
            ],
            'contabilidad' => [
                'configuracion-ver',
                'configuracion-editar',
                'pos-ver',
                'pos-cobro',
                'clientes-ver',
                'clientes-gestionar',
                'cajas-abrir',
                'cajas-cerrar',
                'cajas-ver',
                'inventario-ver',
                'compras-gestionar',
                'productos-gestionar',
                'kardex-ver',
                'reportes-ver',
                'reportes-cajas-ver',
            ],
        ];

        foreach ($roles as $name => $perms) {
            $role = Role::firstOrCreate(['name' => $name]);
            $role->syncPermissions($perms);
        }
    }
}
