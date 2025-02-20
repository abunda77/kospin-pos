<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function show(Category $category)
    {
        return response()->json([
            'status' => 'success',
            'data' => $category->load('products')
        ]);
    }
}
