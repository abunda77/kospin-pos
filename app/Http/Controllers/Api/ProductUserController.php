<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductUserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Sukses mengambil data produk',
            'data' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::with('category')
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sukses mengambil detail produk',
            'data' => $product
        ]);
    }

    public function getProductsByCategory(Request $request, $categoryId)
    {
        $perPage = $request->input('per_page', 10);

        $products = Product::with('category')
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Sukses mengambil data produk berdasarkan kategori',
            'data' => $products
        ]);
    }
}
