@extends('layouts.app')

@section('title', 'Editar caja')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar caja: {{ $cashRegister->name }}</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('restaurant.cash-registers.update', $cashRegister) }}" method="POST">
            @csrf
            @method('PUT')
            @include('restaurant.cash-registers._form')
        </form>
    </div>
@endsection
