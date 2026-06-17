@extends('layouts.app')

@section('title', 'Assign Homework')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Assign Homework'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.homework.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @include('admin.homework.partials.form')
            </form>
        </div>
    </div>
@endsection
