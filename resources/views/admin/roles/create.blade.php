@extends('layouts.app')

@section('title', 'Add Role')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add Role'])

    <form method="POST" action="{{ route('admin.roles.store') }}" novalidate>
        @csrf
        @include('admin.roles.partials.form')
    </form>
@endsection
