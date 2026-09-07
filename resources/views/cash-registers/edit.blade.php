@extends('layouts.app')

@section('title', 'Cerrar caja')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Cerrar caja</h1>
    <p class="mt-1 text-sm text-gray-600">
        Caja: <strong>{{ $session->cashRegister->name }}</strong> · Turno: <strong>{{ $session->shift->name }}</strong> ·
        Abierta por {{ $session->userOpening->name }} el {{ $session->opened_at?->format('d/m/Y H:i') }}.
    </p>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('cash-registers.sessions.update', $session) }}" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label for="closing_amount" class="block text-sm font-medium text-gray-700">Monto de cierre (S/)</label>
                <input id="closing_amount" name="closing_amount" type="number" step="0.01" min="0"
                    value="{{ old('closing_amount', $session->opening_amount) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <p class="mt-1 text-xs text-gray-500">
                    Se registrará automáticamente el usuario y la fecha de cierre.
                </p>
                @error('closing_amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                    class="rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500">
                    Cerrar caja
                </button>
                <a href="{{ route('cash-registers.sessions.index') }}"
                    class="text-sm font-medium text-gray-600 hover:text-gray-500">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection