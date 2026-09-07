@extends('layouts.app')

@section('title', 'Editar categoría de producto')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Editar categoría de producto</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('inventory.product-categories.update', $productCategory) }}" method="POST">
            @csrf
            @method('PUT')
            @include('inventory.product-categories._form')
        </form>
    </div>
@endsection