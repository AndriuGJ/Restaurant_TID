<div class="space-y-8">
    {{-- Icono de comprobantes / logo --}}
    <div class="rounded-md border border-dashed border-gray-300 bg-gray-50 p-5">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Icono para comprobantes / facturación</h2>
        <p class="mt-1 text-xs text-gray-500">Este icono se mostrará en los comprobantes y facturas electrónicas. JPG, PNG o WebP · máx. 2 MB.</p>

        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-md border border-gray-200 bg-white">
                @if ($company->logo)
                    <img id="logo-preview" src="{{ asset('storage/'.$company->logo) }}" alt="Icono de la empresa"
                        class="h-full w-full object-contain p-1.5">
                @else
                    <img id="logo-preview" src="" alt="Icono de la empresa" class="hidden h-full w-full object-contain p-1.5">
                    <svg id="logo-placeholder" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h15a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5H6.75a1.5 1.5 0 01-1.5 1.5v4.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 18.75 7.5 9l3 4.5 1.5-2.25L16.5 15.75"/>
                    </svg>
                @endif
            </div>
            <div class="flex-1">
                <label for="logo" class="inline-flex cursor-pointer items-center gap-2 rounded-md border border-brand-300 bg-white px-4 py-2 text-sm font-semibold text-brand-600 shadow-sm hover:bg-brand-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Subir icono
                </label>
                <input id="logo" name="logo" type="file" accept="image/*" class="hidden">
                <p class="mt-2 text-xs text-gray-500">Si no subes una imagen, se conservará el icono actual.</p>
                @error('logo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Datos principales</h2>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input id="name" name="name" type="text" value="{{ old('name', $company->name ?? '') }}" required
                    class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="commercial_name" class="block text-sm font-medium text-gray-700">Nombre comercial</label>
                <input id="commercial_name" name="commercial_name" type="text" value="{{ old('commercial_name', $company->commercial_name ?? '') }}"
                    class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label for="ruc" class="block text-sm font-medium text-gray-700">RUC</label>
                <input id="ruc" name="ruc" type="text" value="{{ old('ruc', $company->ruc ?? '') }}" required maxlength="11"
                    class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                @error('ruc')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-2">
                <label for="social_reason" class="block text-sm font-medium text-gray-700">Razón social</label>
                <input id="social_reason" name="social_reason" type="text" value="{{ old('social_reason', $company->social_reason ?? '') }}" required
                    class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                @error('social_reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $company->phone ?? '') }}" required maxlength="9"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>
    </div>

    <div class="space-y-6 border-t border-gray-200 pt-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Dirección</h2>

        <div>
            <label for="commercial_address" class="block text-sm font-medium text-gray-700">Dirección comercial</label>
            <input id="commercial_address" name="commercial_address" type="text" value="{{ old('commercial_address', $company->commercial_address ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label for="fiscal_address" class="block text-sm font-medium text-gray-700">Dirección fiscal</label>
            <input id="fiscal_address" name="fiscal_address" type="text" value="{{ old('fiscal_address', $company->fiscal_address ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label for="ubigeo_id" class="block text-sm font-medium text-gray-700">Ubigeo</label>
            <select id="ubigeo_id" name="ubigeo_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Sin ubicación</option>
                @foreach ($ubigeos->groupBy(fn ($u) => $u->department.' / '.$u->province) as $group => $districts)
                    <optgroup label="{{ $group }}">
                        @foreach ($districts as $u)
                            <option value="{{ $u->id }}" @selected(old('ubigeo_id', $company->ubigeo_id ?? '') == $u->id)>
                                {{ $u->district }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('ubigeo_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="space-y-6 border-t border-gray-200 pt-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">SUNAT y certificado digital</h2>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="sol_user" class="block text-sm font-medium text-gray-700">Usuario SOL</label>
                <input id="sol_user" name="sol_user" type="text" value="{{ old('sol_user', $company->sol_user ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div>
                <label for="sol_password" class="block text-sm font-medium text-gray-700">Contraseña SOL</label>
                <input id="sol_password" name="sol_password" type="password" placeholder="{{ $company->sol_password ? '•••••• (dejar vacío para no cambiar)' : '' }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                @error('sol_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="digital_certificate_path" class="block text-sm font-medium text-gray-700">Ruta del certificado digital</label>
            <input id="digital_certificate_path" name="digital_certificate_path" type="text" value="{{ old('digital_certificate_path', $company->digital_certificate_path ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>
    </div>

    <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
        <button type="submit"
            class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
            Guardar
        </button>
        <a href="{{ route('configuration.companies.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500">
            Cancelar
        </a>
    </div>
</div>
