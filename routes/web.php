<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\SteamGuardController;



Route::get('/download/mafile/{orderItem}', [DownloadController::class, 'downloadMafile'])
    ->middleware(['auth', 'owner.check'])
    ->name('download.mafile');
// หน้าหลัก
Route::get('/', [ProductController::class, 'index'])->name('home');

// สินค้า
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [ProductController::class, 'category'])->name('categories.show');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');

// ต้องล็อกอินก่อน
Route::middleware(['auth'])->group(function () {


    Route::get('/orders/{orderItem}/steam-guard', [SteamGuardController::class, 'showSteamGuard'])
        ->name('steam-guard.show');
    Route::get('/api/orders/{orderItem}/steam-guard-code', [SteamGuardController::class, 'getCode'])
        ->name('steam-guard.code');
    // โปรไฟล์
    // Add this to the auth middleware group
    Route::get('/seller-request', [ProfileController::class, 'sellerRequest'])->name('seller.request');
    Route::post('/seller-request', [ProfileController::class, 'storeSellerRequest'])->name('seller.request.store');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ออเดอร์
    Route::resource('orders', OrderController::class);
    Route::post('/products/{product}/buy', [OrderController::class, 'buy'])->name('products.buy');
    // การชำระเงิน
    Route::post('/topup/truemoney/process', [PaymentController::class, 'processTruemoneyTopup'])->name('topup.truemoney.process');

    Route::get('/topup', [PaymentController::class, 'toupIndex'])->name('topup');
    Route::get('/topup/history', [PaymentController::class, 'topupHistory'])->name('topup.history');
    Route::get('/topup/truewallet', [PaymentController::class, 'toupTruemoney'])->name('toupTruemoney');
    Route::get('/topup/chillpay', [PaymentController::class, 'toupChillpay'])->name('toupChillpay');
    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/payments/process', [PaymentController::class, 'process'])->name('payments.process');

    Route::post('/topup/chillpay/process', [PaymentController::class, 'processChillpay'])->name('topup.chillpay.process');
    


    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/payments/process', [PaymentController::class, 'process'])->name('payments.process');
    Route::post('/payments/release/{orderItem}', [PaymentController::class, 'escrowRelease'])->name('payments.release');
    // ข้อความ
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');

    // รีวิว
    Route::post('/reviews/{order}', [ReviewController::class, 'store'])->name('reviews.store');
});

// ผู้ขาย
Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Seller\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', App\Http\Controllers\Seller\ProductController::class);
    Route::resource('orders', App\Http\Controllers\Seller\OrderController::class);
    Route::get('/transactions', [App\Http\Controllers\Seller\DashboardController::class, 'transactions'])->name('transactions');
    Route::post('/products/{orderItem}/deliver', [App\Http\Controllers\Seller\OrderController::class, 'deliverKey'])->name('products.deliver');
});

// แอดมิน
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // แดชบอร์ด
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // จัดการผู้ใช้
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{user}/toggle-verification', [App\Http\Controllers\Admin\UserController::class, 'toggleVerification'])->name('users.toggle-verification');
    Route::post('/users/{user}/adjust-balance', [App\Http\Controllers\Admin\UserController::class, 'adjustBalance'])->name('users.adjust-balance');
    // Add these inside the admin middleware group
    Route::get('/seller-requests', [App\Http\Controllers\Admin\UserController::class, 'sellerRequests'])->name('seller-requests.index');
    Route::post('/seller-requests/{user}/approve', [App\Http\Controllers\Admin\UserController::class, 'approveSellerRequest'])->name('seller-requests.approve');
    Route::post('/seller-requests/{user}/reject', [App\Http\Controllers\Admin\UserController::class, 'rejectSellerRequest'])->name('seller-requests.reject');
    // จัดการสินค้า
    Route::resource('/products', App\Http\Controllers\Admin\ProductController::class);
    Route::post('/products/{product}/change-status', [App\Http\Controllers\Admin\ProductController::class, 'changeStatus'])->name('products.change-status');

    // จัดการหมวดหมู่
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);

    // จัดการออเดอร์
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class);

    // ธุรกรรมการเงิน
    Route::get('/transactions', [App\Http\Controllers\Admin\DashboardController::class, 'transactions'])->name('transactions');

    // รายงาน
    Route::get('/reports/overview', [App\Http\Controllers\Admin\ReportController::class, 'overview'])->name('reports.overview');
    Route::get('/reports/sales', [App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/users', [App\Http\Controllers\Admin\ReportController::class, 'users'])->name('reports.users');
    Route::get('/reports/export/sales', [App\Http\Controllers\Admin\ReportController::class, 'exportSalesCsv'])->name('reports.export.sales');
});

// Authentication Routes
require __DIR__ . '/auth.php';
