@extends('layouts.app')

@section('title', 'Nuevo cliente')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Nuevo cliente</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            @include('customers._form')
        </form>
    </div>
@endsection