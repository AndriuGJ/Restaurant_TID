@extends('layouts.app')

@section('title', 'Editar medio de pago')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Editar: {{ $paymentMethod->name }}</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('configuration.payment-methods.update', $paymentMethod) }}" method="POST">
            @csrf
            @method('PUT')
            @include('configuration.payment-methods._form')
        </form>
    </div>
@endsection
