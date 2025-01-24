<?php

use Illuminate\Support\Facades\Route;
use App\Exports\TemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\StrukController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;



// Route::get('/', [CatalogController::class, 'index'])

// ->name('home');

Route::get('/download-template', function() {
    return Excel::download(new TemplateExport, 'template.xlsx');
})->name('download-template');

Route::get('/struk/{orderId}', [StrukController::class, 'show'])
    ->name('struk');

Route::get('/', [CatalogController::class, 'index'])
    ->name('catalog');

Route::get('/catalog/{category}', [CatalogController::class, 'show']);



Route::get('/cart', [CartController::class, 'index'])
    ->name('cart');
Route::post('/cart/add/{product}', [CartController::class, 'add'])
    ->name('cart.add');
Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])
    ->name('cart.remove');
Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout');
Route::post('/checkout/process', [CheckoutController::class, 'process'])
    ->name('checkout.process');
Route::delete('/cart/{product}', [CartController::class, 'delete'])->name('cart.delete');
Route::get('/check-member/{nik}', [CheckoutController::class, 'checkMember'])->name('checkout.check-member');
