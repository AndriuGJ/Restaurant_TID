@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar producto</h1>

    <div class="mt-6 max-w-2xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('inventory.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            @include('inventory.products._form')
        </form>
    </div>
@endsection