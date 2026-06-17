@extends('layouts.app')

@section('title', 'Admit Student')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Admit Student'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @include('admin.students.partials.form')
            </form>
        </div>
    </div>
@endsection
