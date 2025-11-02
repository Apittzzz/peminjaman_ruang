@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-color: #1B3C53;
        --primary-dark: #214740ff;
    }

    .login-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        background: white;
        width: 24rem;
    }

    .login-header {
        background: var(--primary-color);
        color: white;
        padding: 1.5rem;
        border-radius: 10px 10px 0 0;
        font-size: 1.25rem;
    }

    .login-body {
        padding: 2rem;
    }

    .form-control {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(22, 160, 133, 0.25);
    }

    .btn-primary {
        background: var(--primary-color);
        border-color: var(--primary-color);
        padding: 0.75rem;
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
    }
</style>

<div class="min-vh-60 d-flex justify-content-center align-items-center">
    <div class="login-card">
        <div class="login-header text-center">
            <i class="fas fa-building me-2"></i>
            Login Sistem Peminjaman Ruang
        </div>
        <div class="login-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="username" class="form-label text-muted">Username</label>
                    <input type="text" id="username" name="username" 
                           class="form-control @error('username') is-invalid @enderror" 
                           required autofocus>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label text-muted">Password</label>
                    <input type="password" id="password" name="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
        </div>
    </div>
</div>
@endsection