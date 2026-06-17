@extends('layouts.app')

@section('title', 'Add Lead')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add Admission Lead'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.leads.store') }}" novalidate>
                @csrf
                @include('admin.leads.partials.form')
            </form>
        </div>
    </div>
@endsection
