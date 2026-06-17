@extends('layouts.app')

@section('title', 'Add Teacher')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add Teacher'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.teachers.store') }}" novalidate>
                @csrf
                @include('admin.teachers.partials.form')
            </form>
        </div>
    </div>
@endsection
