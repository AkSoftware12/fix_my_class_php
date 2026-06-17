@extends('layouts.app')

@section('title', 'Edit Online Class')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Online Class: '.$onlineClass->title])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.online-classes.update', $onlineClass) }}" novalidate>
                @csrf
                @method('PUT')
                @include('admin.online-classes.partials.form')
            </form>
        </div>
    </div>
@endsection
