<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['user', 'category']);
        
        // กรองตามหมวดหมู่
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        // กรองตามสถานะ
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // กรองตามผู้ขาย
        if ($request->has('seller_id') && $request->seller_id) {
            $query->where('user_id', $request->seller_id);
        }
        
        // ค้นหาตามชื่อ
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                 ->orWhere('description', 'like', '%' . $request->search . '%');
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
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(20);
        $categories = Category::all();
        $sellers = User::where('role', 'seller')->get();
        
        return view('admin.products.index', compact('products', 'categories', 'sellers'));
    }

    /**
     * แสดงรายละเอียดสินค้า
     */
    public function create()
    {
        $categories = Category::all();
        $sellers = User::where('role', 'seller')->get();
        
        return view('admin.products.create', compact('categories', 'sellers'));
    }
    public function show(Product $product)
    {
        $product->load(['user', 'category', 'orderItems.order']);
        
        return view('admin.products.show', compact('product'));
    }

    /**
     * แสดงฟอร์มแก้ไขสินค้า
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $sellers = User::where('role', 'seller')->get();
        
        return view('admin.products.edit', compact('product', 'categories', 'sellers'));
    }

    /**
     * อัพเดทข้อมูลสินค้า
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'type' => 'required|string|in:steam_key,origin_key,gog_key,uplay_key,battlenet_key,account',
            'status' => 'required|string|in:available,sold,pending',
            'attributes' => 'nullable|array',
            'key_data' => 'nullable|string',
        ]);
        
        $product->category_id = $request->category_id;
        $product->user_id = $request->user_id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->type = $request->type;
        $product->status = $request->status;
        $product->attributes = $request->attributes;
        if ($request->filled('key_data')) {
            $product->key_data = Crypt::encryptString($request->key_data);
        }

        $product->save();
        
        return redirect()->route('admin.products.show', $product)
            ->with('success', 'อัพเดทสินค้าเรียบร้อยแล้ว');
    }

    /**
     * ลบสินค้า
     */
    public function destroy(Product $product)
    {
        // ตรวจสอบว่าสินค้าถูกซื้อไปแล้วหรือไม่
        if ($product->orderItems()->exists()) {
            return redirect()->back()->with('error', 'ไม่สามารถลบสินค้าที่มีการสั่งซื้อแล้วได้');
        }
        
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }
    
    /**
     * เปลี่ยนสถานะสินค้า
     */
    public function changeStatus(Request $request, Product $product)
    {
        $request->validate([
            'status' => 'required|in:available,pending,sold',
        ]);
        
        $product->status = $request->status;
        $product->save();
        
        return redirect()->back()->with('success', 'เปลี่ยนสถานะสินค้าเรียบร้อยแล้ว');
    }
}
