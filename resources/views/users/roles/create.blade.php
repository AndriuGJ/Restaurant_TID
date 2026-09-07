@extends('layouts.app')

@section('title', 'Nuevo rol')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Nuevo rol</h1>

    <div class="mt-6 max-w-3xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('users.roles.store') }}" method="POST">
            @csrf
            @include('users.roles._form')
        </form>
    </div>
@endsection