<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\BannerIklan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->with('category');

        // Filter berdasarkan kategori jika ada
        if ($request->category) {
            $category = Category::where('slug', $request->category)->firstOrFail();
            $query->where('category_id', $category->id);
        }

        // Add search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        $products = $query->paginate(12);

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $activeBanners = BannerIklan::where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->inRandomOrder()
            ->get();

        // Pass category ke view jika ada
        $category = $request->category ? Category::where('slug', $request->category)->first() : null;

        return view('catalog', compact('products', 'categories', 'activeBanners', 'category'));
    }

    public function show(Request $request, Category $category)
    {
        $query = Product::where('is_active', true)
            ->where('category_id', $category->id)
            ->with('category');

        // Add search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        $products = $query->paginate(12);

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $activeBanners = BannerIklan::where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->inRandomOrder()
            ->get();

        return view('catalog', compact('products', 'categories', 'activeBanners', 'category'));
    }

    public function downloadPdf()
    {
        ini_set('memory_limit', '256M');

        $products = Product::where('is_active', true)
            ->with('category')
            ->get();

        $pdf = PDF::loadView('pdf.catalog', compact('products'));

        return $pdf->download('catalog.pdf');
    }

    public function indexMobile(Request $request)
    {
        $query = Product::where('is_active', true)->with('category');

        // Filter berdasarkan kategori jika ada
        if ($request->category) {
            $category = Category::where('slug', $request->category)->firstOrFail();
            $query->where('category_id', $category->id);
        }

        // Add search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Reduce pagination count for faster loading on mobile
        $products = $query->paginate(8);

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $activeBanners = BannerIklan::where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->take(1) // Limit to 1 banner for mobile to save bandwidth
            ->inRandomOrder()
            ->get();

        // Pass category ke view jika ada
        $category = $request->category ? Category::where('slug', $request->category)->first() : null;

        return view('catalog_mobile', compact('products', 'categories', 'activeBanners', 'category'));
    }

    public function showMobile(Request $request, Category $category)
    {
        $query = Product::where('is_active', true)
            ->where('category_id', $category->id)
            ->with('category');

        // Add search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Reduce pagination count for faster loading on mobile
        $products = $query->paginate(8);

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $activeBanners = BannerIklan::where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->take(1) // Limit to 1 banner for mobile to save bandwidth
            ->inRandomOrder()
            ->get();

        return view('catalog_mobile', compact('products', 'categories', 'activeBanners', 'category'));
    }
}
