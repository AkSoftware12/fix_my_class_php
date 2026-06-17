@extends('layouts.app')

@section('title', 'Schedule Online Class')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Schedule Online Class'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.online-classes.store') }}" novalidate>
                @csrf
                @include('admin.online-classes.partials.form')
            </form>
        </div>
    </div>
@endsection
