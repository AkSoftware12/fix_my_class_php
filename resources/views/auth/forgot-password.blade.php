@extends('layouts.guest')

@section('title', 'Forgot password')

@section('content')
    <p class="text-muted small">Enter the email linked to your account and we will send you a password reset link.</p>

    @if (session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="you@example.com" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            <i class="bi bi-envelope me-1"></i> Send reset link
        </button>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Back to sign in
            </a>
        </div>
    </form>
@endsection
