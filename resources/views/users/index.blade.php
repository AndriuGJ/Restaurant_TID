@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Usuarios</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Personas con acceso al sistema y sus roles.</p>
        </div>
        @can('usuarios-gestionar')
            <a href="{{ route('users.create') }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nuevo usuario
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">DNI</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Correo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Cargo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Roles</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->first_name }} {{ $user->last_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->username }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->dni ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->cargo ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($user->roles as $role)
                                    <span class="inline-flex rounded-full bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Sin rol</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $user->status])
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('users.edit', $user),
                                'deleteUrl' => route('users.destroy', $user),
                                'deleteLabel' => 'este usuario',
                                'permission' => 'usuarios-gestionar',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection