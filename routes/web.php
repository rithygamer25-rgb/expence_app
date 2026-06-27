<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return redirect()->route('home');
});

Route::middleware('guest')->group(function () {
    // Login Views and Action Handlers
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Registration Views and Action Handlers
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    
    // Global User Sign Out System Session Teardown
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard View Framework Workspaces
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/scan', [DashboardController::class, 'scan'])->name('scan');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    // Basic Profile Navigation & Configuration Handlers
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile/info', [DashboardController::class, 'updateInfo'])->name('profile.update-info');
    Route::put('/profile/password', [DashboardController::class, 'updatePassword'])->name('profile.update-password');

    // Restful Actions explicitly handled via the Expense Controller
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

    // AI Scan Route for Receipt Processing
    Route::post('/scan/ai-process', [DashboardController::class, 'aiScan'])->name('expenses.ai-scan');
    
    // DEBUG: Test Groq API connectivity
    Route::get('/test-groq-api', [DashboardController::class, 'testGroqApi'])->name('test.groq-api');

    // Dynamic Custom Categories CRUD Action Maps 
    Route::post('/profile/categories', [ExpenseController::class, 'storeCategory'])->name('categories.store');
    Route::put('/profile/categories/{id}', [ExpenseController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/profile/categories/{id}', [ExpenseController::class, 'destroyCategory'])->name('categories.destroy');

    // Dynamic Custom Payment Methods CRUD Action Maps
    Route::post('/profile/payment-methods', [ExpenseController::class, 'storePaymentMethod'])->name('payment-methods.store');
    Route::put('/profile/payment-methods/{id}', [ExpenseController::class, 'updatePaymentMethod'])->name('payment-methods.update');
    Route::delete('/profile/payment-methods/{id}', [ExpenseController::class, 'destroyPaymentMethod'])->name('payment-methods.destroy');
    
    // ADD THIS PIPELINE TARGET ROUTE TO YOUR AUTH GROUP:
    Route::post('/scan/ai-process', [DashboardController::class, 'aiScan'])->name('expenses.ai-scan');
});