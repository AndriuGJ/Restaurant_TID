@can($permission ?? 'configuracion-editar')
    <div class="flex items-center justify-end gap-3">
        <a href="{{ $editUrl }}"
            class="text-sm font-medium text-brand-600 hover:text-brand-500">Editar</a>

        <form method="POST" action="{{ $deleteUrl }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="text-sm font-medium text-red-600 hover:text-red-500"
                onclick="return confirm('¿Eliminar {{ $deleteLabel ?? 'este registro' }}?')">Eliminar</button>
        </form>
    </div>
@endcan
