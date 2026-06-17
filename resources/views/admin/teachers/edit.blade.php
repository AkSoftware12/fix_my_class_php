@extends('layouts.app')

@section('title', 'Edit Teacher')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Teacher: '.$teacher->user?->name])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" novalidate>
                @csrf
                @method('PUT')
                @include('admin.teachers.partials.form')
            </form>
        </div>
    </div>
@endsection
