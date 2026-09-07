@extends('layouts.app')

@section('title', 'Tipos de documento')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tipos de documento</h1>
            <p class="mt-1 text-sm text-gray-600">Configura los documentos de identidad y comprobantes.</p>
        </div>
        @can('configuracion-editar')
            <a href="{{ route('configuration.document-types.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nuevo tipo
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Sigla</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Límite</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($documentTypes as $documentType)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $documentType->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $documentType->nomenclature ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $documentType->character_limit ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm capitalize text-gray-600">{{ $documentType->type === 'invoice' ? 'Comprobante' : 'Identificación' }}</td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $documentType->status])
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('configuration.document-types.edit', $documentType),
                                'deleteUrl' => route('configuration.document-types.destroy', $documentType),
                                'deleteLabel' => 'este tipo de documento',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay tipos de documento registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $documentTypes->links() }}
    </div>
@endsection
