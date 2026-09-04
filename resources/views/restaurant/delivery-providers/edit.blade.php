@extends('layouts.app')

@section('title', 'Editar proveedor de delivery')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar proveedor: {{ $deliveryProvider->name }}</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('restaurant.delivery-providers.update', $deliveryProvider) }}" method="POST">
            @csrf
            @method('PUT')
            @include('restaurant.delivery-providers._form')
        </form>
    </div>
@endsection
