@extends('layouts.app')

@section('title', 'Editar cliente')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Editar cliente</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')
            @include('customers._form')
        </form>
    </div>
@endsection