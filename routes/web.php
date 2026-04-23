<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Invoices
Route::resource('invoices', InvoiceController::class);
Route::patch('invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment');

// Customers
Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
Route::resource('customers', CustomerController::class)->only(['index', 'store', 'update', 'destroy']);

// Items
Route::get('items/search', [ItemController::class, 'search'])->name('items.search');
Route::resource('items', ItemController::class)->only(['index', 'store', 'update', 'destroy']);

// Settings
Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

// GST Reports
Route::get('reports/gst', [App\Http\Controllers\GstReportController::class, 'index'])->name('reports.gst');
