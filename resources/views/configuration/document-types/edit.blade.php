@extends('layouts.app')

@section('title', 'Editar tipo de documento')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Editar: {{ $documentType->name }}</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('configuration.document-types.update', $documentType) }}" method="POST">
            @csrf
            @method('PUT')
            @include('configuration.document-types._form')
        </form>
    </div>
@endsection
