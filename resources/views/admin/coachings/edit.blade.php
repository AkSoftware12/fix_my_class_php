@extends('layouts.app')

@section('title', 'Edit Coaching')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Coaching: '.$coaching->name])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.coachings.update', $coaching) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @include('admin.coachings.partials.form')
            </form>
        </div>
    </div>
@endsection
