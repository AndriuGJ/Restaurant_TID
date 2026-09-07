@extends('layouts.app')

@section('title', 'Nuevo proveedor')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Nuevo proveedor</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('inventory.suppliers.store') }}" method="POST">
            @csrf
            @include('inventory.suppliers._form')
        </form>
    </div>
@endsection