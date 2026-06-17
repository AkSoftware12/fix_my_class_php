@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Student: '.$student->user?->name])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @include('admin.students.partials.form')
            </form>
        </div>
    </div>
@endsection
