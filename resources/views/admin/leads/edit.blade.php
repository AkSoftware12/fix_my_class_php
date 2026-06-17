@extends('layouts.app')

@section('title', 'Edit Lead')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Lead: '.$lead->student_name])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.leads.update', $lead) }}" novalidate>
                @csrf
                @method('PUT')
                @include('admin.leads.partials.form')
            </form>
        </div>
    </div>
@endsection
