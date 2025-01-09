<?php

use Illuminate\Support\Facades\Route;
use App\Exports\TemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\StrukController;



// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/download-template', function() {
    return Excel::download(new TemplateExport, 'template.xlsx');
})->name('download-template');

Route::get('/struk/{orderId}', [StrukController::class, 'show'])
    ->name('struk');
