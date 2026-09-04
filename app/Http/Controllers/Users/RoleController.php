<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreRoleRequest;
use App\Http\Requests\Users\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('users')->orderBy('name')->get(['id', 'name']);

        return view('users.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = $this->groupedPermissions();
        $role = new Role;

        return view('users.roles.create', compact('permissions', 'role'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::firstOrCreate([
            'name' => Str::slug($request->name),
        ]);

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('users.roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role): View
    {
        $permissions = $this->groupedPermissions();

        return view('users.roles.edit', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update([
            'name' => Str::slug($request->name),
        ]);

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('users.roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'administrador') {
            return redirect()
                ->route('users.roles.index')
                ->withErrors('El rol administrador no puede eliminarse.');
        }

        $role->delete();

        return redirect()->route('users.roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }

    /** @return Collection<string, Collection<int, Permission>> */
    private function groupedPermissions(): Collection
    {
        $permissions = Permission::orderBy('name')->get(['id', 'name']);

        return $permissions->groupBy(function (Permission $permission) {
            return match (true) {
                Str::startsWith($permission->name, 'configuracion') => 'Configuración',
                Str::startsWith($permission->name, 'pos-') => 'Punto de venta',
                Str::startsWith($permission->name, 'clientes-') => 'Clientes',
                Str::startsWith($permission->name, 'cajas-') => 'Cajas',
                Str::startsWith($permission->name, ['inventario', 'productos', 'compras', 'kardex']) => 'Inventario',
                Str::startsWith($permission->name, 'reportes') => 'Reportes',
                Str::startsWith($permission->name, ['usuarios', 'roles']) => 'Usuarios',
                default => 'General',
            };
        });
    }
}
