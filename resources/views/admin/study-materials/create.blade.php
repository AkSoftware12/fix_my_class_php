@extends('layouts.app')

@section('title', 'Upload Study Material')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Upload Study Material'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.study-materials.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @include('admin.study-materials.partials.form')
            </form>
        </div>
    </div>
@endsection
