@extends('layouts.app')

@section('title', 'Home Dashboard - ExpenseTracker')
@section('page-title', 'Welcome back, ' . auth()->user()->name . '!')
@section('page-subtitle', 'Manage your financials easily')

@section('content')
<div class="row g-3 mb-4">
    <!-- Stat Block 1: This Month Total -->
    <div class="col-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <small class="text-muted d-block">This Month</small>
                    <h4 class="fw-bold my-2">${{ number_format($thisMonthSum, 2) }}</h4>
                    <span class="text-success small fw-semibold"><i class="bi bi-wallet2"></i> Active Track</span>
                </div>
                <div class="badge bg-primary-subtle text-primary p-2 rounded"><i class="bi bi-currency-dollar fs-5"></i></div>
            </div>
        </div>
    </div>
    
    <!-- Stat Block 2: Last Month Total -->
    <div class="col-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <small class="text-muted d-block">Last Month</small>
                    <h4 class="fw-bold my-2">${{ number_format($lastMonthSum, 2) }}</h4>
                    <span class="text-muted small">{{ now()->subMonth()->format('M Y') }}</span>
                </div>
                <div class="badge bg-danger-subtle text-danger p-2 rounded"><i class="bi bi-calendar3 fs-5"></i></div>
            </div>
        </div>
    </div>
    
    <!-- Stat Block 3: Total Expenses Count -->
    <div class="col-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <small class="text-muted d-block">Expenses Logged</small>
                    <h4 class="fw-bold my-2">{{ $expensesCount }}</h4>
                    <span class="text-muted small">Total items record</span>
                </div>
                <div class="badge bg-success-subtle text-success p-2 rounded"><i class="bi bi-receipt fs-5"></i></div>
            </div>
        </div>
    </div>
    
    <!-- Stat Block 4: Average Cost per Item -->
    <div class="col-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <small class="text-muted d-block">Average</small>
                    <h4 class="fw-bold my-2">${{ number_format($averageExpense, 2) }}</h4>
                    <span class="text-muted small">Per transaction item</span>
                </div>
                <div class="badge bg-warning-subtle text-warning p-2 rounded"><i class="bi bi-bar-chart fs-5"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Panel -->
<div class="card p-4 border-0 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="m-0 fw-bold text-dark">Recent Expenses</h6>
        <a href="{{ route('expenses.index') }}" class="text-decoration-none small fw-semibold">View All</a>
    </div>

    @if($recentExpenses->count() > 0)
        <!-- Dynamic Data Grid List -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <tbody class="small text-dark">
                    @foreach($recentExpenses as $expense)
                        <tr>
                            <td class="ps-2 fw-semibold">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-{{ $expense->category->color_theme ?? 'secondary' }}-subtle text-{{ $expense->category->color_theme ?? 'secondary' }} p-2 rounded-circle d-none d-sm-block">
                                        <i class="bi {{ $expense->category->icon ?? 'bi-shop' }} fs-6 d-block"></i>
                                    </div>
                                    {{ $expense->location }}
                                </div>
                            </td>
                            <td class="text-muted">{{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}</td>
                            <td><span class="badge bg-{{ $expense->category->color_theme ?? 'secondary' }}-subtle text-{{ $expense->category->color_theme ?? 'secondary' }} px-2.5 py-1.5 rounded-pill">{{ $expense->category->name }}</span></td>
                            <td class="text-muted d-none d-md-table-cell"><i class="bi bi-wallet2 me-1"></i> {{ $expense->paymentMethod->name }}</td>
                            <td class="text-end pe-2 fw-bold text-danger">-${{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Empty State UI Illustration Placeholder -->
        <div class="text-center py-5">
            <div class="text-muted display-1 mb-2"><i class="bi bi-shield-exclamation text-body-tertiary"></i></div>
            <p class="text-muted small mb-0">Track your budget items easily.</p>
            <p class="text-muted small">Start by scanning your first receipt.</p>
        </div>
    @endif
</div>
@endsection
