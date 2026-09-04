@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar usuario</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $user->first_name }} {{ $user->last_name }}</p>
        </div>
    </div>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            @include('users._form')
        </form>
    </div>
@endsection