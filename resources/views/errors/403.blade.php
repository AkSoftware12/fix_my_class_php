@extends('layouts.guest')

@section('title', 'Access denied')

@section('content')
    <div class="text-center">
        <div class="display-4 fw-bold text-danger mb-2">403</div>
        <h2 class="h5 fw-bold">Access denied</h2>
        <p class="text-muted small">You do not have permission to access this page. If you believe this is a mistake, contact your administrator.</p>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('login') }}" class="btn btn-primary mt-2">
            <i class="bi bi-arrow-left me-1"></i> Go back
        </a>
    </div>
@endsection
