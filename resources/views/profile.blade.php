@extends('layouts.app')

@section('title', 'My Profile - ExpenseTracker')
@section('page-title', 'Account Settings')
@section('page-subtitle', 'Manage your profile details, custom categories, and payment types')

@section('content')
<!-- Display Success Banner -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Display Global Validation Error Loop -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- Left Column: Personal Information Form -->
    <div class="col-lg-7">
        <div class="card p-4 border-0 shadow-sm h-100">
            <div class="d-flex align-items-center gap-3 mb-4 pb-2 border-bottom">
                <div class="bg-primary-subtle text-primary rounded-circle p-2">
                    <i class="bi bi-person-gear fs-4 d-block"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0 text-dark">Personal Information</h6>
                    <small class="text-muted">Update your display information and contact channels</small>
                </div>
            </div>

            <form action="{{ route('profile.update-info') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label small fw-semibold text-muted">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Email address</label>
                        <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary px-4 fw-semibold mt-4"><i class="bi bi-check2-circle me-2"></i>Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Right Column: Update Password Form -->
    <div class="col-lg-5">
        <div class="card p-4 border-0 shadow-sm h-100">
            <div class="d-flex align-items-center gap-3 mb-4 pb-2 border-bottom">
                <div class="bg-danger-subtle text-danger rounded-circle p-2">
                    <i class="bi bi-shield-lock fs-4 d-block"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0 text-dark">Security Update</h6>
                    <small class="text-muted">Revoke or update system entry passwords</small>
                </div>
            </div>

            <form action="{{ route('profile.update-password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Current Password</label>
                    <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password" required>
                </div>
                <button type="submit" class="btn btn-dark w-100 fw-semibold mt-3"><i class="bi bi-key me-2"></i>Update Password</button>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- CRUD MANAGEMENT SECTIONS                                                 -->
<!-- ========================================================================= -->
<div class="row g-4">
    
    <!-- CATEGORY CRUD SECTION -->
    <div class="col-md-6">
        <div class="card p-4 border-0 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-tags text-primary fs-5"></i>
                    <h6 class="fw-bold m-0 text-dark">My Categories</h6>
                </div>
                <button class="btn btn-primary btn-sm px-2.5 py-1 rounded" data-bs-toggle="collapse" data-bs-target="#addCategoryCollapse">
                    <i class="bi bi-plus-lg me-1"></i> Add New
                </button>
            </div>

            <!-- Dynamic Quick Add Drawer -->
            <div class="collapse mb-3" id="addCategoryCollapse">
                <div class="p-3 bg-light rounded border border-dashed">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-1 fw-semibold">Category Name</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Entertainment, Health" required>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1 fw-semibold">Bootstrap Icon</label>
                                <input type="text" name="icon" class="form-control form-control-sm" value="bi-tag" placeholder="e.g. bi-heart">
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1 fw-semibold">Color Theme</label>
                                <select name="color_theme" class="form-select form-select-sm">
                                    <option value="primary">Blue (Primary)</option>
                                    <option value="success">Green (Success)</option>
                                    <option value="warning">Yellow (Warning)</option>
                                    <option value="danger">Red (Danger)</option>
                                    <option value="info">Cyan (Info)</option>
                                    <option value="dark">Black (Dark)</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark btn-sm w-100 mt-1">Create Category</button>
                    </form>
                </div>
            </div>

            <!-- Responsive List Table Data Grid -->
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm table-hover align-middle mb-0">
                    <tbody class="small text-dark">
                        @forelse($categories as $category)
                            <tr>
                                <td class="ps-2">
                                    <span class="badge bg-{{ $category->color_theme ?? 'secondary' }}-subtle text-{{ $category->color_theme ?? 'secondary' }} p-2 rounded me-2">
                                        <i class="bi {{ $category->icon ?? 'bi-tag' }}"></i>
                                    </span>
                                    <span class="fw-semibold">{{ $category->name }}</span>
                                    @if(is_null($category->user_id))
                                        <small class="badge bg-light text-muted border ms-1" style="font-size: 9px;">System Default</small>
                                    @endif
                                </td>
                                <td class="text-end pe-2">
                                    @if(!is_null($category->user_id))
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this custom category record permanently?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link link-danger p-1 shadow-none border-0"><i class="bi bi-trash3 fs-6"></i></button>
                                        </form>
                                    @else
                                        <span class="text-muted small px-2">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-center text-muted py-4 small">No categories registered.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

     <!-- PAYMENT METHOD CRUD SECTION -->
    <div class="col-md-6">
        <div class="card p-4 border-0 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-credit-card text-danger fs-5"></i>
                    <h6 class="fw-bold m-0 text-dark">My Payment Methods</h6>
                </div>
                <button class="btn btn-danger btn-sm px-2.5 py-1 rounded" data-bs-toggle="collapse" data-bs-target="#addPaymentCollapse">
                    <i class="bi bi-plus-lg me-1"></i> Add New
                </button>
            </div>

            <!-- Dynamic Quick Add Drawer -->
            <div class="collapse mb-3" id="addPaymentCollapse">
                <div class="p-3 bg-light rounded border border-dashed">
                    <form action="{{ route('payment-methods.store') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-1 fw-semibold">Method Name</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. ABA Bank, Wing Wallet" required>
                        </div>
                        <button type="submit" class="btn btn-dark btn-sm w-100 mt-1">Create Method</button>
                    </form>
                </div>
            </div>

            <!-- Responsive List Table Data Grid -->
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm table-hover align-middle mb-0">
                    <tbody class="small text-dark">
                        @forelse($paymentMethods as $method)
                            <tr>
                                <td class="ps-2 py-2 fw-semibold">
                                    <i class="bi bi-wallet2 me-2 text-muted"></i>{{ $method->name }}
                                    @if(is_null($method->user_id))
                                        <small class="badge bg-light text-muted border ms-1" style="font-size: 9px;">Default</small>
                                    @endif
                                </td>
                                <td class="text-end pe-2">
                                    @if(!is_null($method->user_id))
                                        <!-- Edit Action Button -->
                                        <button class="btn btn-link link-secondary p-1 border-0" data-bs-toggle="modal" data-bs-target="#editPaymentModal{{ $method->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <!-- Delete Action Button -->
                                        <form action="{{ route('payment-methods.destroy', $method->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payment channel permanently?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link link-danger p-1 border-0"><i class="bi bi-trash3"></i></button>
                                        </form>

                                        <!-- UPDATE MODAL FOR PAYMENT METHOD -->
                                        <div class="modal fade" id="editPaymentModal{{ $method->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content border-0 shadow text-start">
                                                    <div class="modal-header py-2 border-bottom-0">
                                                        <h6 class="modal-title fw-bold">Edit Method</h6>
                                                        <button type="button" class="btn-close small" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body py-2">
                                                        <form action="{{ route('payment-methods.update', $method->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mb-3">
                                                                <label class="form-label small text-muted mb-1 fw-semibold">Method Name</label>
                                                                <input type="text" name="name" class="form-control form-control-sm" value="{{ $method->name }}" required>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary btn-sm w-100">Update Method</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small px-2">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-center text-muted py-4 small">No custom payment options registered.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

