@extends('layouts.app')

@section('title', 'Add Batch')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add Batch'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.batches.store') }}" novalidate>
                @csrf
                @include('admin.batches.partials.form')
            </form>
        </div>
    </div>
@endsection
