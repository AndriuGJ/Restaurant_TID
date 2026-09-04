@extends('layouts.app')

@section('title', 'Nuevo tipo de documento')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nuevo tipo de documento</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('configuration.document-types.store') }}" method="POST">
            @csrf
            @include('configuration.document-types._form')
        </form>
    </div>
@endsection
