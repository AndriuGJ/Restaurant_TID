@extends('layouts.app')

@section('title', 'Nueva categoría de compra')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nueva categoría de compra</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('inventory.purchase-categories.store') }}" method="POST">
            @csrf
            @include('inventory.purchase-categories._form')
        </form>
    </div>
@endsection