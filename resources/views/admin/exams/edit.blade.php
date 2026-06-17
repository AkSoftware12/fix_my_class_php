@extends('layouts.app')

@section('title', 'Edit Exam')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Exam: '.$exam->title])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.exams.update', $exam) }}" novalidate>
                @csrf
                @method('PUT')
                @include('admin.exams.partials.form')
            </form>
        </div>
    </div>
@endsection
