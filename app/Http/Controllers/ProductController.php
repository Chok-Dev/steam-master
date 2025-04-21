<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query()->available();
        
        // กรองตามหมวดหมู่
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        // กรองตามราคา
        if ($request->has('price_min') && $request->price_min) {
            $query->where('price', '>=', $request->price_min);
        }
        
        if ($request->has('price_max') && $request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }
        
        // เรียงลำดับ
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('views', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(12);
        $categories = Category::all();
        
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * แสดงรายละเอียดสินค้า
     */
    public function show(Product $product)
    {
        // เพิ่มจำนวนการเข้าชม
        $product->incrementViews();
        
        // ดึงข้อมูลสินค้าอื่นของผู้ขายรายนี้
        $otherProducts = Product::where('user_id', $product->user_id)
            ->where('id', '!=', $product->id)
            ->available()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // ดึงข้อมูลสินค้าที่คล้ายกัน
        $similarProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->available()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // ดึงรีวิวของผู้ขาย
        $reviews = Review::where('seller_id', $product->user_id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        return view('products.show', compact('product', 'otherProducts', 'similarProducts', 'reviews'));
    }

    /**
     * แสดงสินค้าตามหมวดหมู่
     */
    public function category(Category $category)
    {
        $products = Product::where('category_id', $category->id)
            ->available()
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        $categories = Category::all();
        
        return view('products.index', compact('products', 'categories', 'category'));
    }

    /**
     * ค้นหาสินค้า
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->available()
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        $categories = Category::all();
        
        return view('products.search', compact('products', 'categories', 'query'));
    }
}
