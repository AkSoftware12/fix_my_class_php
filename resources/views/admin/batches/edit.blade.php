@extends('layouts.app')

@section('title', 'Edit Batch')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Batch: '.$batch->name])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.batches.update', $batch) }}" novalidate>
                @csrf
                @method('PUT')
                @include('admin.batches.partials.form')
            </form>
        </div>
    </div>
@endsection
