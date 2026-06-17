@extends('layouts.app')

@section('title', 'Edit Homework')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Homework: '.$homework->title])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.homework.update', $homework) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @include('admin.homework.partials.form')
            </form>
        </div>
    </div>
@endsection
