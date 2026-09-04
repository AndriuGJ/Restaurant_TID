@extends('layouts.app')

@section('title', 'Nuevo salón')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Nuevo salón</h1>

    <div class="mt-6 max-w-lg rounded-md border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('restaurant.halls.store') }}" method="POST">
            @csrf
            @include('restaurant.halls._form')
        </form>
    </div>
@endsection
