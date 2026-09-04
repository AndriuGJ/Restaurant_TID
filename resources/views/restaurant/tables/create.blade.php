@extends('layouts.app')

@section('title', 'Nueva mesa')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Nueva mesa</h1>

    <div class="mt-6 max-w-lg rounded-md border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('restaurant.tables.store') }}" method="POST">
            @csrf
            @include('restaurant.tables._form')
        </form>
    </div>
@endsection
