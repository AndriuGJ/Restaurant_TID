@extends('layouts.app')

@section('title', 'Editar mesa')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Editar mesa: {{ $table->name }}</h1>

    <div class="mt-6 max-w-lg rounded-md border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('restaurant.tables.update', $table) }}" method="POST">
            @csrf
            @method('PUT')
            @include('restaurant.tables._form')
        </form>
    </div>
@endsection
