@extends('layouts.app')

@section('title', 'Edit Study Material')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Material: '.$material->title])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.study-materials.update', $material) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @include('admin.study-materials.partials.form')
            </form>
        </div>
    </div>
@endsection
