@extends('layouts.app')

@section('title', 'Analytics - ExpenseTracker')
@section('page-title', 'Analytics & Reports')
@section('page-subtitle', 'Gain critical breakdown insight on tracking habits')

@section('content')
<div class="card p-4 border-0 shadow-sm mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="m-0 fw-bold text-dark">System Aggregates</h6>
            <small class="text-muted">Overview of historical records</small>
        </div>
        <!-- ADDED: Export Action Button Trigger -->
        <button class="btn btn-outline-dark btn-sm fw-semibold px-3" onclick="exportExpensesToCSV()">
            <i class="bi bi-download me-2"></i>Export as CSV
        </button>
    </div>
    <div class="row g-2 text-center py-2">
        <div class="col-4 border-end">
            <h5 class="fw-bold m-0 text-dark">${{ number_format($totalSpent, 2) }}</h5>
            <small class="text-muted" style="font-size: 11px;">Total Volume</small>
        </div>
        <div class="col-4 border-end">
            <h5 class="fw-bold m-0 text-dark">${{ number_format($averageExpense, 2) }}</h5>
            <small class="text-muted" style="font-size: 11px;">Per Expense</small>
        </div>
        <div class="col-4">
            <h5 class="fw-bold text-danger m-0">${{ number_format($topExpense, 2) }}</h5>
            <small class="text-muted" style="font-size: 11px;">Top Single Cost</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Category Doughnut Card Layout Module -->
    <div class="col-md-6">
        <div class="card p-4 border-0 shadow-sm h-100">
            <h6 class="fw-bold mb-3 small text-muted text-uppercase">By Category</h6>
            @if(count($categoryData) > 0)
                <div style="position: relative; height: 260px;" class="d-flex justify-content-center">
                    <canvas id="categoryChart"></canvas>
                </div>
            @else
                <div class="text-center py-5 text-muted small border rounded-3 bg-light border-dashed h-100 d-flex align-items-center justify-content-center">
                    No categorical records found
                </div>
            @endif
        </div>
    </div>

    <!-- Monthly Trend Card Layout Module -->
    <div class="col-md-6">
        <div class="card p-4 border-0 shadow-sm h-100">
            <h6 class="fw-bold mb-3 small text-muted text-uppercase">Monthly Trend</h6>
            @if(count($trendData) > 0)
                <div style="position: relative; height: 260px;">
                    <canvas id="trendChart"></canvas>
                </div>
            @else
                <div class="text-center py-5 text-muted small border rounded-3 bg-light border-dashed h-100 d-flex align-items-center justify-content-center">
                    No monthly trend history found
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Inject Chart.js Core Library Engine directly via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // 1. Render Category Doughnut Chart Data
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            const catData = @json($categoryData);
            
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(catData),
                    datasets: [{
                        data: Object.values(catData),
                        backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            position: 'bottom', 
                            labels: { boxWidth: 10, padding: 10 } 
                        } 
                    }
                }
            });
        }

        // 2. Render Monthly Progression Line Chart Data
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
            const trendData = @json($trendData);
            
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: Object.keys(trendData),
                    datasets: [{
                        data: Object.values(trendData),
                        borderColor: '#111111',
                        backgroundColor: 'rgba(0, 0, 0, 0.02)',
                        fill: true,
                        tension: 0.25,
                        borderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f8f9fa' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    });

    // 3. Client-Side CSV File Data Exporter Engine
    function exportExpensesToCSV() {
        const categories = @json($categoryData);
        if (!categories || Object.keys(categories).length === 0) {
            alert('No asset logging data found to export.');
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Category Name,Total Spent ($)\n";

        Object.keys(categories).forEach(function (key) {
            csvContent += `"${key}",${parseFloat(categories[key]).toFixed(2)}\n`;
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "ExpenseTracker_Summary_Report.csv");
        document.body.appendChild(link);
        
        link.click();
        document.body.removeChild(link);
    }
</script>
@endpush

