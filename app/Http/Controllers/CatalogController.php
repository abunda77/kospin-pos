<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\BannerIklan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->paginate(12);

        $activeBanners = BannerIklan::where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->inRandomOrder()
            ->get();

        return view('catalog', compact('products', 'activeBanners'));
    }

    public function show(Category $category)
    {
        $products = Product::where('is_active', true)
            ->where('category_id', $category->id)
            ->with('category')
            ->paginate(12);

        return view('catalog', compact('products'));
    }

    public function downloadPdf()
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->get();

        $pdf = PDF::loadView('pdf.catalog', compact('products'));

        return $pdf->download('catalog.pdf');
    }
}
