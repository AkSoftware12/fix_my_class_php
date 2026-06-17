@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    @include('admin.partials.page-header', ['title' => 'My Profile'])

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-person me-1 text-primary"></i> Profile Details</h2>
                    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ $user->avatar_url }}" class="avatar-lg" alt="">
                            <div class="flex-grow-1">
                                <label class="form-label" for="avatar">Change avatar</label>
                                <input type="file" id="avatar" name="avatar" accept="image/*"
                                       class="form-control @error('avatar') is-invalid @enderror">
                                @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required" for="name">Name</label>
                                <input type="text" id="name" name="name" required
                                       class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required" for="email">Email</label>
                                <input type="email" id="email" name="email" required
                                       class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mobile">Mobile</label>
                                <input type="text" id="mobile" name="mobile"
                                       class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $user->mobile) }}">
                                @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-check-lg me-1"></i>Save Profile</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-shield-lock me-1 text-primary"></i> Change Password</h2>
                    <form method="POST" action="{{ route('admin.profile.password') }}" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label required" for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required
                                   class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label required" for="password">New Password</label>
                            <input type="password" id="password" name="password" required
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label required" for="password_confirmation">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-key me-1"></i>Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
