@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Usuarios</h1>
            <p class="mt-1 text-sm text-gray-600">Personas con acceso al sistema y sus roles.</p>
        </div>
        @can('usuarios-gestionar')
            <a href="{{ route('users.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nuevo usuario
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">DNI</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Correo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Cargo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Roles</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->username }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->dni ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->cargo ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($user->roles as $role)
                                    <span class="inline-flex rounded-full bg-brand-100 px-2 py-1 text-xs font-semibold text-brand-700">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-500">Sin rol</span>
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
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
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