@php
    $selectedPermissions = old('permissions', $role->permissions->pluck('name')->all() ?? []);
@endphp
<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del rol</label>
        <input id="name" name="name" type="text" value="{{ old('name', $role->name ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        @error('name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Se guardará en formato slug (ej. "Administrador" → "administrador").</p>
    </div>

    <div>
        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permisos</span>
        <div class="mt-2 space-y-4 rounded-md border border-gray-200 p-4 dark:border-gray-800">
            @foreach ($permissions as $group => $perms)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $group }}</p>
                    <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach ($perms as $permission)
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                    {{ in_array($permission->name, $selectedPermissions) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800">
                                <span class="text-sm text-gray-800 dark:text-gray-200">{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Guardar
        </button>
        <a href="{{ route('users.roles.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500 dark:text-gray-400">
            Cancelar
        </a>
    </div>
</div>