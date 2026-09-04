@extends('layouts.app')

@section('title', 'Editar empresa')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar empresa</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $company->name }}</p>
        </div>
        <a href="{{ route('configuration.companies.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-500">
            Volver
        </a>
    </div>

    <div class="mt-6 rounded-md border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('configuration.companies.update', $company) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('configuration.companies._form')
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('logo')?.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.getElementById('logo-preview');
                const placeholder = document.getElementById('logo-placeholder');

                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                }
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        });
    </script>
@endpush