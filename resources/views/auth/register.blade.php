@extends('layouts.app')

@section('title', 'Create Account - ExpenseTracker')

@section('auth-content')
<div class="card p-4 border-0 shadow-sm auth-card">
    <div class="text-center mb-4">
        <div class="d-flex align-items-center justify-content-center mb-2">
            <i class="bi bi-wallet2 text-primary fs-3 me-2"></i>
            <span class="fw-bold fs-4">ExpenseTracker</span>
        </div>
        <h6 class="fw-bold text-dark mt-3">Create account</h6>
        <small class="text-muted">Start managing your personal items details</small>
    </div>

    <form action="{{ url('/register') }}" method="POST">
        @csrf
        
        <!-- Error Summary Alert standard validation mapping array block loop -->
        @if ($errors->any())
            <div class="alert alert-danger p-2 small">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif
        
        <div class="mb-2">
            <label class="form-label small fw-semibold text-muted">Full Name</label>
            <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name') }}" placeholder="Enter your full name" required>
        </div>
        <div class="mb-2">
            <label class="form-label small fw-semibold text-muted">Email address</label>
            <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email') }}" placeholder="Enter your email" required>
        </div>
        <div class="mb-2">
            <label class="form-label small fw-semibold text-muted">Password</label>
            <input type="password" name="password" class="form-control form-control-sm" placeholder="Create a password" required>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold text-muted">Confirm Password</label>
            <!-- Notice name="password_confirmation" property is strictly required by Laravel -->
            <input type="password" name="password_confirmation" class="form-control form-control-sm" placeholder="Confirm your password" required>
        </div>
        <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold">Sign Up</button>
    </form>
    
    <div class="text-center mt-3">
        <small class="text-muted">Already have an account? <a href="/login" class="text-decoration-none fw-semibold">Sign In</a></small>
    </div>
</div>
@endsection
