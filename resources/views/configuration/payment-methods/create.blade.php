@extends('layouts.app')

@section('title', 'Nuevo medio de pago')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Nuevo medio de pago</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('configuration.payment-methods.store') }}" method="POST">
            @csrf
            @include('configuration.payment-methods._form')
        </form>
    </div>
@endsection
