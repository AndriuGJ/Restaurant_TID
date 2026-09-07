@extends('layouts.app')

@section('title', 'Abrir caja')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Abrir caja</h1>
    <p class="mt-1 text-sm text-gray-600">
        Se indicará automáticamente el usuario y la fecha de apertura.
    </p>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('cash-registers.sessions.store') }}" method="POST">
            @csrf

            <div>
                <label for="cash_register_id" class="block text-sm font-medium text-gray-700">Caja</label>
                <select id="cash_register_id" name="cash_register_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">Seleccione una caja</option>
                    @foreach ($cashRegisters as $cashRegister)
                        <option value="{{ $cashRegister->id }}" @selected(old('cash_register_id') == $cashRegister->id)>
                            {{ $cashRegister->name }} ({{ $cashRegister->cash_register_sessions_count }} sesiones)
                        </option>
                    @endforeach
                </select>
                @error('cash_register_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <label for="shift_id" class="block text-sm font-medium text-gray-700">Turno</label>
                <select id="shift_id" name="shift_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">Seleccione un turno</option>
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}" @selected(old('shift_id') == $shift->id)>{{ $shift->name }}</option>
                    @endforeach
                </select>
                @error('shift_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <label for="opening_amount" class="block text-sm font-medium text-gray-700">Monto de apertura (S/)</label>
                <input id="opening_amount" name="opening_amount" type="number" step="0.01" min="0"
                    value="{{ old('opening_amount', $selectedOpeningAmount) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <p class="mt-1 text-xs text-gray-500">
                    Se pre-llena con el monto de cierre de la última sesión cerrada de la caja. Puedes ajustarlo.
                </p>
                @error('opening_amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                    class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                    Abrir caja
                </button>
                <a href="{{ route('cash-registers.sessions.index') }}"
                    class="text-sm font-medium text-gray-600 hover:text-gray-500">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const select = document.getElementById('cash_register_id');
            const amount = document.getElementById('opening_amount');
            const suggestions = @json($suggestedOpenings);
            const initial = amount.value;

            select.addEventListener('change', () => {
                const suggested = suggestions[select.value];
                amount.value = suggested ?? initial;
            });
        })();
    </script>
@endpush