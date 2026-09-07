@extends('layouts.app')

@section('title', 'Editar impresora')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Editar impresora: {{ $printer->name }}</h1>

    <div class="mt-6 max-w-lg rounded-md border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('restaurant.printers.update', $printer) }}" method="POST">
            @csrf
            @method('PUT')
            @include('restaurant.printers._form')
        </form>
    </div>
@endsection