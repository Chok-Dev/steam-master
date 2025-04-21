<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->get();
        
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * แสดงฟอร์มสร้างหมวดหมู่ใหม่
     */
    public function create()
    {
        $categories = Category::all();
        
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * บันทึกหมวดหมู่ใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048', // รูปภาพขนาดไม่เกิน 2MB
        ]);
        
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->parent_id = $request->parent_id;
        
        // อัพโหลดรูปภาพ (ถ้ามี)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $category->image = str_replace('public/', '', $path);
        }
        
        $category->save();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'สร้างหมวดหมู่ใหม่เรียบร้อยแล้ว');
    }

    /**
     * แสดงฟอร์มแก้ไขหมวดหมู่
     */
    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)->get();
        
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * อัพเดทข้อมูลหมวดหมู่
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048', // รูปภาพขนาดไม่เกิน 2MB
        ]);
        
        // ป้องกันการเลือกตัวเองเป็น parent
        if ($request->parent_id == $category->id) {
            return redirect()->back()->with('error', 'ไม่สามารถเลือกตัวเองเป็นหมวดหมู่หลักได้');
        }
        
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->parent_id = $request->parent_id;
        
        // อัพโหลดรูปภาพใหม่ (ถ้ามี)
        if ($request->hasFile('image')) {
            // ลบรูปเก่า (ถ้ามี)
            if ($category->image) {
                Storage::delete('public/' . $category->image);
            }
            
            // อัพโหลดรูปใหม่
            $path = $request->file('image')->store('categories', 'public');
            $category->image = str_replace('public/', '', $path);
        }
        
        $category->save();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'อัพเดทหมวดหมู่เรียบร้อยแล้ว');
    }

    /**
     * ลบหมวดหมู่
     */
    public function destroy(Category $category)
    {
        // ตรวจสอบว่ามีสินค้าในหมวดหมู่นี้หรือไม่
        if ($category->products()->exists()) {
            return redirect()->back()->with('error', 'ไม่สามารถลบหมวดหมู่ที่มีสินค้าอยู่ได้');
        }
        
        // ตรวจสอบว่ามีหมวดหมู่ย่อยหรือไม่
        if ($category->children()->exists()) {
            return redirect()->back()->with('error', 'ไม่สามารถลบหมวดหมู่ที่มีหมวดหมู่ย่อยได้');
        }
        
        // ลบรูปภาพ (ถ้ามี)
        if ($category->image) {
            Storage::delete('public/' . $category->image);
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'ลบหมวดหมู่เรียบร้อยแล้ว');
    }
}
