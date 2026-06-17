@extends('layouts.app')

@section('title', 'Add User')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add User'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @include('admin.users.partials.form')
            </form>
        </div>
    </div>
@endsection
