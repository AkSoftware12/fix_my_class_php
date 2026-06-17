@extends('layouts.app')

@section('title', 'Edit Notice')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Notice: '.$notice->title])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.notices.update', $notice) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @include('admin.notices.partials.form')
            </form>
        </div>
    </div>
@endsection
