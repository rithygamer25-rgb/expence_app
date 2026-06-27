@extends('layouts.app')

@section('title', 'Scan Receipt - ExpenseTracker')
@section('page-title', 'Scan Receipt')
@section('page-subtitle', 'Convert receipt images to items logs instantly using Google AI')

@section('content')
<div class="row g-4">
    <div class="col-lg-5 col-xl-6">
        <div class="card p-4 border-0 shadow-sm text-center h-100 d-flex flex-column justify-content-between">
            <div class="text-start"><h6 class="fw-bold mb-0">Scan Receipt</h6></div>
            
            <!-- Upload Container Dropzone Area -->
            <div class="upload-zone border border-2 border-dashed rounded-3 p-5 my-4 bg-light position-relative flex-grow-1 d-flex align-items-center justify-content-center">
                <input type="file" id="receiptUpload" name="receipt_image" class="opacity-0 position-absolute w-100 h-100 top-0 start-0 cursor-pointer" accept="image/*">
                
                <!-- State UI 1: Idle Drop Area -->
                <div id="uploadIdleState">
                    <div class="btn btn-primary rounded-circle p-3 pt-2 pb-2 mb-3 shadow-sm"><i class="bi bi-camera fs-3 d-block"></i></div>
                    <p class="fw-semibold mb-1 small">Upload Receipt</p>
                    <small class="text-muted d-block" style="font-size: 12px;">Take picture or upload image</small>
                </div>

                <!-- State UI 2: Processing AI Scanner (Initially Hidden) -->
                <div id="uploadLoadingState" class="d-none py-4">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                    <p class="fw-semibold mb-1 small text-primary">GROQ AI Scanning...</p>
                    <small class="text-muted d-block" style="font-size: 12px;">Extracting receipt details</small>
                </div>

                <!-- State UI 3: Image Preview Screen Layout Holder (Initially Hidden) -->
                <div id="uploadPreviewState" class="d-none w-100">
                    <img id="receiptImagePreview" src="#" class="img-fluid rounded shadow-sm mb-2" style="max-height: 220px; object-fit: contain;">
                    <small class="text-muted d-block cursor-pointer"><i class="bi bi-arrow-clockwise me-1"></i>Click image to change file</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-7 col-xl-6">
        <div class="card p-4 border-0 shadow-sm">
            <h6 class="fw-bold mb-4">Expense Details</h6>
            
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-sm-8">
                        <label class="form-label small fw-semibold text-muted">Location</label>
                        <input type="text" id="expenseLocation" name="location" class="form-control" placeholder="Store name" value="{{ old('location') }}" required>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label class="form-label small fw-semibold text-muted">Date</label>
                        <input type="date" id="expenseDate" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" id="expenseAmount" name="amount" step="0.01" class="form-control" placeholder="0.00" value="{{ old('amount') }}" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Category</label>
                        <select id="expenseCategory" name="category_id" class="form-select" required>
                            <option value="" selected disabled>Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label small fw-semibold text-muted">Payment method</label>
                        <select id="expenseMethod" name="payment_method_id" class="form-select" required>
                            <option value="" selected disabled>Select Method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mt-3"><i class="bi bi-floppy me-2"></i>Save Expense</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.getElementById('receiptUpload');
        const idleState = document.getElementById('uploadIdleState');
        const loadingState = document.getElementById('uploadLoadingState');
        const previewState = document.getElementById('uploadPreviewState');
        const previewImage = document.getElementById('receiptImagePreview');
        const removeButton = document.getElementById('removeReceiptBtn'); // Added reset anchor target

        // 1. Core Upload Handler
        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                const file = e.target.files[0]; 
                if (!file) return;

                // Instantly display image preview while keeping loading spinner active
                const reader = new FileReader();
                reader.onload = function (event) {
                    previewImage.src = event.target.result;
                    idleState.classList.add('d-none');
                    loadingState.classList.remove('d-none');
                    previewState.classList.remove('d-none'); 
                };
                reader.readAsDataURL(file);

                // Package file binaries into Form Data arrays 
                const formData = new FormData();
                formData.append('receipt', file);
                formData.append('_token', '{{ csrf_token() }}');

                // Dispatch secure asynchronous network pipeline straight to Laravel
                fetch('{{ route("expenses.ai-scan") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => {
                    const contentType = response.headers.get("content-type");
                    if (!response.ok) {
                        if (contentType && contentType.includes("application/json")) {
                            return response.json().then(err => { 
                                throw {
                                    message: err.message || 'Request failed',
                                    status: response.status,
                                    ...err
                                };
                            });
                        }
                        throw { 
                            message: `HTTP Error ${response.status}`, 
                            status: response.status 
                        };
                    }
                    return response.json();
                })
                .then(data => {
                    loadingState.classList.add('d-none');

                    if (data.success) {
                        // Autofill text input rows using properties returned from AI
                        if (data.data.location) document.getElementById('expenseLocation').value = data.data.location;
                        if (data.data.date) document.getElementById('expenseDate').value = data.data.date;
                        if (data.data.amount) document.getElementById('expenseAmount').value = parseFloat(data.data.amount).toFixed(2);
                        
                        // Run matching selectors logic
                        matchDropdownOption('expenseCategory', data.data.category);
                        matchDropdownOption('expenseMethod', data.data.payment_method);
                    } else {
                        alert('AI Scanner Warning: ' + (data.message || 'Details could not be parsed clearly. Please input data manually.'));
                    }
                })
                // Replace the error block in your script with this updated alert parser
.catch(error => {
    resetUploadState();
    console.error('AI Scanner Error:', error);
    
    let errorMsg = 'Server error during image processing.';
    
    // Handle different error types
    if (error && typeof error === 'object') {
        // If it's a structured error response from the backend
        if (error.message) {
            errorMsg = error.message;
        }
        if (error.status === 401 || error.status === 403) {
            errorMsg = '❌ Authentication Error: Invalid API key. Check GEMINI_API_KEY in .env';
        } else if (error.status === 400) {
            errorMsg = '❌ Bad Request: ' + (error.message || 'Invalid request format');
        } else if (error.status === 429) {
            errorMsg = '⏱️ Rate Limited: Too many requests. Try again later.';
        } else if (error.status === 500) {
            errorMsg = '⚠️ Server Error: ' + (error.message || 'Google API server error');
        }
    } else if (typeof error === 'string') {
        errorMsg = error;
    }
    
    alert('AI Scanner Error:\n\n' + errorMsg + '\n\nPlease check the server logs for more details.');
});
            });
        }

        // 2. Clear / Reset Action Listener
        if (removeButton) {
            removeButton.addEventListener('click', function (e) {
                e.preventDefault();
                resetUploadState();
            });
        }

        // Central UI Reset Utility Method
        function resetUploadState() {
            loadingState.classList.add('d-none');
            previewState.classList.add('d-none');
            idleState.classList.remove('d-none');
            if (fileInput) fileInput.value = ""; 
            if (previewImage) previewImage.src = "";
        }

        // Dropdown option mapper helper
        function matchDropdownOption(elementId, textValue) {
            if (!textValue) return;
            const select = document.getElementById(elementId);
            if (!select) return;
            const normalizedText = textValue.toLowerCase().replace(/[^a-z0-9]/g, '');
            
            for (let i = 0; i < select.options.length; i++) {
                const optionText = select.options[i].text.toLowerCase().replace(/[^a-z0-9]/g, '');
                if (optionText.includes(normalizedText) || normalizedText.includes(optionText)) {
                    select.selectedIndex = i;
                    break;
                }
            }
        }
    });
</script>
@endpush
