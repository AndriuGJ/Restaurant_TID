@php
    $selectedRoles = old('roles', $user->roles->pluck('name')->all() ?? []);
@endphp
<div class="space-y-6">
    <div>
        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
        <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        @error('first_name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</label>
        <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        @error('last_name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="dni" class="block text-sm font-medium text-gray-700 dark:text-gray-300">DNI</label>
            <input id="dni" name="dni" type="text" value="{{ old('dni', $user->dni ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            @error('dni')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="cargo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cargo</label>
            <input id="cargo" name="cargo" type="text" value="{{ old('cargo', $user->cargo ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        @error('email')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de usuario</label>
        <input id="username" name="username" type="text" value="{{ old('username', $user->username ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        @error('username')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Contraseña{{ isset($user) ? ' (dejar en blanco para mantener)' : '' }}
            </label>
            <input id="password" name="password" type="password"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>
    </div>

    <div>
        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</span>
        <div class="mt-2 space-y-2 rounded-md border border-gray-200 p-4 dark:border-gray-800">
            @forelse ($roles as $role)
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                        {{ in_array($role->name, $selectedRoles) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800">
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $role->name }}</span>
                </label>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay roles disponibles. Crea uno primero.</p>
            @endforelse
        </div>
        @error('roles')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
            <input id="status" name="status" type="checkbox" value="1"
                {{ old('status', $user->status ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800">
            Activo
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Guardar
        </button>
        <a href="{{ route('users.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500 dark:text-gray-400">
            Cancelar
        </a>
    </div>
</div>