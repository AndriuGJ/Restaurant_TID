@extends('layouts.app')

@section('title', 'Nuevo producto')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Nuevo producto</h1>

    <div class="mt-6 max-w-2xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('inventory.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('inventory.products._form')
        </form>
    </div>
@endsection