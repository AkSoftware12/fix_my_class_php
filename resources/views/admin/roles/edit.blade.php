@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Role: '.$role->name])

    <form method="POST" action="{{ route('admin.roles.update', $role) }}" novalidate>
        @csrf
        @method('PUT')
        @include('admin.roles.partials.form')
    </form>
@endsection
