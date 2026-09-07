@extends('layouts.app')

@section('title', 'Nuevo turno')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Nuevo turno</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('restaurant.shifts.store') }}" method="POST">
            @csrf
            @include('restaurant.shifts._form')
        </form>
    </div>
@endsection
