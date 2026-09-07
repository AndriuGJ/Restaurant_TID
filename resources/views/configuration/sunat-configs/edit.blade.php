@extends('layouts.app')

@section('title', 'Editar configuración SUNAT')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Editar configuración SUNAT</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('configuration.sunat-configs.update', $sunatConfig) }}" method="POST">
            @csrf
            @method('PUT')
            @include('configuration.sunat-configs._form')
        </form>
    </div>
@endsection