@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit User: '.$user->name])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @include('admin.users.partials.form')
            </form>
        </div>
    </div>
@endsection
