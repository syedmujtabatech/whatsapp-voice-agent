<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SimulatorController;
use App\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

// Redirect root to Simulator
Route::get('/', function () {
    return redirect()->route('simulator.index');
});

// WhatsApp Live Voice Simulator
Route::prefix('simulator')->name('simulator.')->group(function () {
    Route::get('/', [SimulatorController::class, 'index'])->name('index');
    Route::post('/send', [SimulatorController::class, 'sendMessage'])->name('send');
    Route::post('/reset', [SimulatorController::class, 'reset'])->name('reset');
});

// Admin Management & Analytics Dashboard
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Voice Orders
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{id}', [DashboardController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/status', [DashboardController::class, 'updateOrderStatus'])->name('orders.status');

    // Voice Conversations & Audio Logs
    Route::get('/conversations', [DashboardController::class, 'conversations'])->name('conversations.index');

    // Product Inventory
    Route::get('/products', [DashboardController::class, 'products'])->name('products.index');
    Route::post('/products/save', [DashboardController::class, 'saveProduct'])->name('products.save');
    Route::post('/products/{id}/save', [DashboardController::class, 'saveProduct'])->name('products.update');

    // Settings & Integration
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [DashboardController::class, 'saveSettings'])->name('settings.save');
});

// Official WhatsApp Cloud API Webhook Endpoints
Route::get('/api/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify']);
Route::post('/api/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle']);
