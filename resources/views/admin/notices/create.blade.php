@extends('layouts.app')

@section('title', 'Publish Notice')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Publish Notice'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.notices.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @include('admin.notices.partials.form')
            </form>
        </div>
    </div>
@endsection
