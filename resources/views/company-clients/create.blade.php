@extends('layouts.app')

@section('title', 'Nueva empresa cliente')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Nueva empresa cliente</h1>

    <div class="mt-6 max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('customers.companies.store') }}" method="POST">
            @csrf
            @include('company-clients._form')
        </form>
    </div>
@endsection