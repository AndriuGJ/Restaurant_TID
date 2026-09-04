@extends('layouts.app')

@section('title', 'Editar proveedor')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar proveedor</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('inventory.suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')
            @include('inventory.suppliers._form')
        </form>
    </div>
@endsection