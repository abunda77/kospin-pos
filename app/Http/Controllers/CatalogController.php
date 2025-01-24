<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->paginate(12);

        return view('catalog', compact('products'));
    }
}
