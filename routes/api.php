<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductUserController;
use App\Http\Controllers\Api\CheckoutUserController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Api\WebhookController;

Route::post('login', [AuthController::class, 'login']);

Route::apiResource('products', ProductController::class)->middleware(['auth:sanctum']);
Route::get('products/barcode/{barcode}', [ProductController::class, 'showByBarcode'])->middleware(['auth:sanctum']);
Route::get('payment-methods', [PaymentMethodController::class, 'index'])->middleware(['auth:sanctum']);
Route::apiResource('orders', OrderController::class)->middleware(['auth:sanctum']);
Route::get('setting', [SettingController::class, 'index'])->middleware(['auth:sanctum']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API routes without authentication
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);
Route::get('products-public', [ProductUserController::class, 'index']);
Route::get('products-public/{id}', [ProductUserController::class, 'show']);
Route::get('products-public/category/{categoryId}', [ProductUserController::class, 'getProductsByCategory']);

// Checkout routes
Route::get('payment-methods-public', [CheckoutUserController::class, 'getPaymentMethods']);
Route::get('check-member/{nik}', [CheckoutUserController::class, 'checkMember']);
Route::post('checkout', [CheckoutUserController::class, 'process']);
Route::get('orders-public/{orderId}', [CheckoutUserController::class, 'getOrderDetail']);

// DEPRECATED: Midtrans notification handler dalam CheckoutController
// Route::post('payment/notification/midtrans', [CheckoutController::class, 'handleNotification']);

// Midtrans notification handler menggunakan WebhookController yang lebih lengkap
Route::post('/midtrans/notification', [WebhookController::class, 'handle']);


