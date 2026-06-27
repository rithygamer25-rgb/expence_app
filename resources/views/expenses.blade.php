@extends('layouts.app')

@section('title', 'All Expenses - ExpenseTracker')
@section('page-title', 'All Expenses')
@section('page-subtitle', 'Review, filter, and manage your complete transaction history')

@section('content')
<!-- Display Confirmation Toast Banners -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- ADVANCED FILTER BAR CARDS CONTROL SECTION -->
<div class="card p-3 border-0 shadow-sm mb-4 bg-white">
    <div class="row g-2 align-items-end">
        <!-- Filter 1: Category Selection Dropdown -->
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-tag me-1"></i>Category</label>
            <select id="filterCategory" class="form-select form-select-sm">
                <option value="all" selected>All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ strtolower($category->name) }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter 2: Payment Method Dropdown -->
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-credit-card me-1"></i>Method</label>
            <select id="filterMethod" class="form-select form-select-sm">
                <option value="all" selected>All Methods</option>
                @foreach($paymentMethods as $method)
                    <option value="{{ strtolower($method->name) }}">{{ $method->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter 3: From Start Date Selector -->
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-calendar-event me-1"></i>From</label>
            <input type="date" id="filterStartDate" class="form-control form-control-sm">
        </div>

        <!-- Filter 4: To End Date Selector -->
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-calendar-check me-1"></i>To</label>
            <input type="date" id="filterEndDate" class="form-control form-control-sm">
        </div>

        <!-- Action Control Buttons Column -->
        <div class="col-12 col-md-4">
            <div class="row g-2">
                <!-- Reset Button Controller Action -->
                <div class="col-4">
                    <button id="resetFiltersBtn" class="btn btn-outline-secondary btn-sm w-100 fw-semibold py-1.5">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
                <!-- Export Filtered Data Action Button -->
                <div class="col-8">
                    <button id="exportFilteredBtn" class="btn btn-dark btn-sm w-100 fw-semibold py-1.5">
                        <i class="bi bi-download me-1"></i> Export Filtered CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card p-4 border-0 shadow-sm">
    <!-- Top Search and Sort Row -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h6 class="m-0 fw-bold text-dark">Transaction Records</h6>
            <small class="text-muted">Review items logged across all parameters</small>
        </div>
        <div class="d-flex gap-2 w-100 w-sm-auto">
            <div class="input-group input-group-sm max-search-width">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="tableSearch" class="form-control border-start-0" placeholder="Search locations...">
            </div>
            <select id="tableSort" class="form-select form-select-sm w-auto">
                <option value="newest" selected>Newest Logs</option>
                <option value="oldest">Oldest Logs</option>
            </select>
        </div>
    </div>

    @if($expenses->count() > 0)
        <!-- Responsive Data Table Framework Grid -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="expensesTable">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th scope="col" class="ps-3" style="width: 30%;">Store / Location</th>
                        <th scope="col" style="width: 20%;">Date</th>
                        <th scope="col" style="width: 20%;">Category</th>
                        <th scope="col" style="width: 20%;">Method</th>
                        <th scope="col" class="text-end pe-3" style="width: 10%;">Amount</th>
                    </tr>
                </thead>
                <tbody class="small text-dark" id="expenseTableBody">
                    @foreach($expenses as $expense)
                        <!-- Data properties added here to feed the filter matrix arrays -->
                        <tr class="expense-row" 
                            data-date="{{ $expense->date }}" 
                            data-category="{{ strtolower($expense->category->name) }}" 
                            data-method="{{ strtolower($expense->paymentMethod->name) }}">
                            
                            <td class="ps-3 fw-semibold search-location">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-{{ $expense->category->color_theme ?? 'secondary' }}-subtle text-{{ $expense->category->color_theme ?? 'secondary' }} p-2 rounded-circle d-none d-sm-block">
                                        <i class="bi {{ $expense->category->icon ?? 'bi-shop' }} fs-6 d-block"></i>
                                    </div>
                                    <span class="location-text">{{ $expense->location }}</span>
                                </div>
                            </td>
                            <!-- Added dedicated raw text data tags to target values during extraction -->
                            <td class="text-muted raw-date-cell" data-raw-date="{{ $expense->date }}">{{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}</td>
                            <td class="search-category" data-raw-category="{{ $expense->category->name }}">
                                <span class="badge bg-{{ $expense->category->color_theme ?? 'secondary' }}-subtle text-{{ $expense->category->color_theme ?? 'secondary' }} px-2.5 py-1.5 rounded-pill">
                                    {{ $expense->category->name }}
                                </span>
                            </td>
                            <td class="text-muted search-method" data-raw-method="{{ $expense->paymentMethod->name }}"><i class="bi bi-wallet2 me-1"></i> {{ $expense->paymentMethod->name }}</td>
                            <!-- Added numeric data tag attribute for raw money formatting values -->
                            <td class="text-end pe-3 fw-bold text-danger raw-amount-cell" data-raw-amount="{{ $expense->amount }}">-${{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Filter Engine Fallback Empty State -->
        <div id="noResultsMessage" class="text-center py-5 d-none">
            <div class="text-muted display-4 mb-3"><i class="bi bi-funnel-fill text-body-tertiary"></i></div>
            <p class="fw-semibold mb-1 small text-dark">No matching expenses found</p>
            <small class="text-muted">Try clearing your select filters or search criteria fields</small>
        </div>
    @else
        <div class="text-center py-5">
            <div class="text-muted display-4 mb-3"><i class="bi bi-calendar-minus text-body-tertiary"></i></div>
            <p class="fw-semibold mb-1 small text-dark">No expenses found</p>
            <small class="text-muted">start by insert data</small>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Input Element Selectors
        const searchInput = document.getElementById('tableSearch');
        const sortSelect = document.getElementById('tableSort');
        const filterCategory = document.getElementById('filterCategory');
        const filterMethod = document.getElementById('filterMethod');
        const filterStartDate = document.getElementById('filterStartDate');
        const filterEndDate = document.getElementById('filterEndDate');
        const resetBtn = document.getElementById('resetFiltersBtn');
        const exportBtn = document.getElementById('exportFilteredBtn');

        // Event Listener Hooks
        if (searchInput) searchInput.addEventListener('input', runGlobalFilterMatrix);
        if (filterCategory) filterCategory.addEventListener('change', runGlobalFilterMatrix);
        if (filterMethod) filterMethod.addEventListener('change', runGlobalFilterMatrix);
        if (filterStartDate) filterStartDate.addEventListener('change', runGlobalFilterMatrix);
        if (filterEndDate) filterEndDate.addEventListener('change', runGlobalFilterMatrix);

        if (sortSelect) sortSelect.addEventListener('change', sortTableRows);
        if (resetBtn) resetBtn.addEventListener('click', clearAllFiltersForm);
        if (exportBtn) exportBtn.addEventListener('click', exportFilteredDataToCSV);

        // --- 1. Multi-Parameter Unified Filter Matrix Engine ---
        function runGlobalFilterMatrix() {
            const searchVal = searchInput.value.toLowerCase().trim();
            const catVal = filterCategory.value;
            const methodVal = filterMethod.value;
            const startVal = filterStartDate.value ? new Date(filterStartDate.value) : null;
            const endVal = filterEndDate.value ? new Date(filterEndDate.value) : null;

            if (startVal) startVal.setHours(0,0,0,0);
            if (endVal) endVal.setHours(23,59,59,999);

            const rows = document.querySelectorAll('.expense-row');
            let matchCounter = 0;

            rows.forEach(row => {
                const rowLocation = row.querySelector('.location-text').textContent.toLowerCase();
                const rowCategory = row.getAttribute('data-category');
                const rowMethod = row.getAttribute('data-method');
                const rowDate = new Date(row.getAttribute('data-date'));
                rowDate.setHours(0,0,0,0);

                // Evaluation Flags
                const matchSearch = rowLocation.includes(searchVal);
                const matchCategory = (catVal === 'all' || rowCategory === catVal);
                const matchMethod = (methodVal === 'all' || rowMethod === methodVal);
                const matchDateRange = (!startVal || rowDate >= startVal) && (!endVal || rowDate <= endVal);

                if (matchSearch && matchCategory && matchMethod && matchDateRange) {
                    row.removeAttribute('style'); // Reveal Row
                    matchCounter++;
                } else {
                    row.setAttribute('style', 'display: none !important;'); // Hide Row
                }
            });

            // Master Toggle Manager
            const noResultsCard = document.getElementById('noResultsMessage');
            const tableContainer = document.querySelector('.table-responsive');

            if (matchCounter === 0 && rows.length > 0) {
                noResultsCard.classList.remove('d-none');
                if (tableContainer) tableContainer.classList.add('d-none');
            } else {
                noResultsCard.classList.add('d-none');
                if (tableContainer) tableContainer.classList.remove('d-none');
            }
        }

        // --- 2. Chronological Row Sorter Engine ---
        function sortTableRows() {
            const tableBody = document.getElementById('expenseTableBody');
            if (!tableBody) return;

            const rowsArray = Array.from(document.querySelectorAll('.expense-row'));
            const sortOrder = sortSelect.value;

            rowsArray.sort((rowA, rowB) => {
                const dateA = new Date(rowA.getAttribute('data-date'));
                const dateB = new Date(rowB.getAttribute('data-date'));
                return sortOrder === 'newest' ? dateB - dateA : dateA - dateB;
            });

            rowsArray.forEach(row => tableBody.appendChild(row));
        }

        // --- 3. Filter Reset Form State Controller ---
        function clearAllFiltersForm() {
            if (searchInput) searchInput.value = '';
            if (filterCategory) filterCategory.value = 'all';
            if (filterMethod) filterMethod.value = 'all';
            if (filterStartDate) filterStartDate.value = '';
            if (filterEndDate) filterEndDate.value = '';
            
            runGlobalFilterMatrix();
        }

        // --- 4. Export Visible/Filtered Rows to CSV Downloader Engine ---
        function exportFilteredDataToCSV() {
            const allRows = document.querySelectorAll('.expense-row');
            const visibleRows = [];

            allRows.forEach(row => {
                if (row.style.display !== 'none') {
                    visibleRows.push(row);
                }
            });

            if (visibleRows.length === 0) {
                alert('No active row matches found matching your filters to export.');
                return;
            }

            let csvRows = [];
            csvRows.push(["Store Location", "Transaction Date", "Category", "Payment Method", "Amount ($)"].join(','));

            visibleRows.forEach(row => {
                const store = `"${row.querySelector('.location-text').textContent.replace(/"/g, '""')}"`;
                const date = row.querySelector('.raw-date-cell').getAttribute('data-raw-date');
                const category = row.querySelector('.search-category').getAttribute('data-raw-category');
                const method = row.querySelector('.search-method').getAttribute('data-raw-method');
                const amount = row.querySelector('.raw-amount-cell').getAttribute('data-raw-amount');

                csvRows.push([store, date, category, method, amount].join(','));
            });

            const csvString = csvRows.join("\n");
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            const downloadUrl = URL.createObjectURL(blob);
            
            const link = document.createElement("a");
            link.setAttribute("href", downloadUrl);
            link.setAttribute("download", "Filtered_Expense_Report.csv");
            document.body.appendChild(link);
            
            link.click();
            document.body.removeChild(link);
        }
    });
</script>
@endpush
