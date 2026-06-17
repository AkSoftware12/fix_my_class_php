@extends('layouts.app')

@section('title', 'Add Coaching')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add Coaching Centre'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.coachings.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @include('admin.coachings.partials.form')
            </form>
        </div>
    </div>
@endsection
