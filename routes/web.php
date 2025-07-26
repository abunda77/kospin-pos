<?php

use Illuminate\Support\Facades\Route;
use App\Exports\TemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\StrukController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PageController;



// Route::get('/', [CatalogController::class, 'index'])

// ->name('home');

Route::get('/download-template', function () {
    return Excel::download(new TemplateExport, 'template.xlsx');
})->name('download-template');

Route::get('/struk/{orderId}', [StrukController::class, 'show'])
    ->name('struk');

Route::get('/', [CatalogController::class, 'index'])
    ->name('home');

Route::get('/catalog', [CatalogController::class, 'index'])
    ->name('catalog');

Route::get('/catalog/download-pdf', [CatalogController::class, 'downloadPdf'])->name('catalog.download-pdf');
Route::get('/catalog/{category}', [CatalogController::class, 'show'])->name('catalog.show');

// Mobile catalog routes
Route::get('/m/catalog', [CatalogController::class, 'indexMobile'])
    ->name('catalog.mobile');
Route::get('/m/catalog/{categorySlug}', [CatalogController::class, 'showMobile'])
    ->name('catalog.mobile.show');

// Static pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Preference setting route
Route::get('/set-view-preference/{preference}', function ($preference) {
    if (in_array($preference, ['mobile', 'desktop'])) {
        session(['view_preference' => $preference]);
    }
    return redirect()->back();
})->name('set.view.preference');



Route::get('/cart', [CartController::class, 'index'])
    ->name('cart');
Route::post('/cart/add/{product}', [CartController::class, 'add'])
    ->name('cart.add');
Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])
    ->name('cart.remove');
// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/process-payment', [CheckoutController::class, 'processPayment'])->name('checkout.process-payment');
Route::get('/thank-you/{order}', [CheckoutController::class, 'thankYou'])->name('thank-you');
// Route::get('/generate-pdf/{order}', [CheckoutController::class, 'generatePdf'])->name('checkout.generate-pdf');
Route::get('/generate-pdf/{order}', [CheckoutController::class, 'generatePdf'])->name('order.pdf');
Route::get('/check-status/{orderId}', [CheckoutController::class, 'checkTransactionStatus'])->name('checkout.check-status');
Route::delete('/cart/{product}', [CartController::class, 'delete'])->name('cart.delete');
Route::get('/check-member/{nik}', [CheckoutController::class, 'checkMember'])->name('checkout.check-member');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
// Route::get('/order-pdf/{order}', [CheckoutController::class, 'generatePdf'])->name('order.pdf');

// Midtrans redirect URL handlers
Route::get('/payment/finish', [CheckoutController::class, 'finishPayment'])->name('payment.finish');
Route::get('/payment/unfinish', [CheckoutController::class, 'unfinishPayment'])->name('payment.unfinish');
Route::get('/payment/error', [CheckoutController::class, 'errorPayment'])->name('payment.error');
