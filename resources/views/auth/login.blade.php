@extends('layouts.app')

@section('title', 'Login - ExpenseTracker')

@section('auth-content')
<div class="card p-4 border-0 shadow-sm auth-card">
    <div class="text-center mb-4">
        <div class="d-flex align-items-center justify-content-center mb-2">
            <img class="fs-4 me-2" width="50" src="{{asset('icons/logo.png')}}" alt="ExpenseTracker Logo">
            <span class="fw-bold fs-4">ExpenseTracker</span>
        </div>
        <h6 class="fw-bold text-dark mt-3">Welcome back!</h6>
        <small class="text-muted">Sign in to your account</small>
    </div>

    <!-- Ready for standard Laravel route injection form mapping -->
    <form action="{{ url('/login') }}" method="POST">
        @csrf
        
        <!-- Error Alert Display Banner if authentication login fields mismatch -->
        @if ($errors->has('email'))
            <div class="alert alert-danger p-2 small">{{ $errors->first('email') }}</div>
        @endif
    
        <div class="mb-3">
            <label class="form-label small fw-semibold text-muted">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold text-muted">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input type="checkbox" name="rememberMe" class="form-check-input" id="rememberMe">
                <label class="form-check-label small text-muted" for="rememberMe">Remember me</label>
            </div>
        </div>
        <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold">Sign In</button>
    </form>
    

    <div class="text-center my-3">
        <small class="text-muted">Don't have an account yet? <a href="/register" class="text-decoration-none fw-semibold">Sign Up</a></small>
    </div>
</div>
@endsection
