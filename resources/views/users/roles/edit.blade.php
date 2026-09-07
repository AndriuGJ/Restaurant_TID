@extends('layouts.app')

@section('title', 'Editar rol')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Editar rol</h1>

    <div class="mt-6 max-w-3xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('users.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            @include('users.roles._form')
        </form>
    </div>
@endsection