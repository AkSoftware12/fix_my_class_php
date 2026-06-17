@extends('layouts.app')

@section('title', 'Create Exam')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Create Exam'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.exams.store') }}" novalidate>
                @csrf
                @include('admin.exams.partials.form')
            </form>
        </div>
    </div>
@endsection
