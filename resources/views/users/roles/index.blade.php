@extends('layouts.app')

@section('title', 'Roles y permisos')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles y permisos</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Define los roles y el conjunto de permisos de cada uno.</p>
        </div>
        @can('roles-gestionar')
            <a href="{{ route('users.roles.create') }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nuevo rol
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Rol</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Usuarios</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($roles as $role)
                    <tr>
                        <td class="px-6 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">{{ $role->name }}</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400">{{ $role->users_count }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('users.roles.edit', $role) }}"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Editar</a>
                                @if ($role->name !== 'administrador')
                                    <form method="POST" action="{{ route('users.roles.destroy', $role) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-sm font-medium text-red-600 hover:text-red-500"
                                            onclick="return confirm('¿Eliminar este rol?')">Eliminar</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No hay roles registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection