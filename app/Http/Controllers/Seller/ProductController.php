<?php

namespace App\Http\Controllers\Seller;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = auth()->user()->products()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('seller.products.index', compact('products'));
    }

    /**
     * แสดงฟอร์มสร้างสินค้าใหม่
     */
    public function create()
    {
        $categories = Category::all();

        return view('seller.products.create', compact('categories'));
    }

    /**
     * บันทึกสินค้าใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'type' => 'required|string|in:steam_key,origin_key,gog_key,uplay_key,battlenet_key,account',
            'attributes' => 'nullable|array',
            'key_data' => 'nullable|string',
        ]);

        $product = new Product();
        $product->user_id = auth()->id();
        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name) . '-' . Str::random(5);
        $product->description = $request->description;
        $product->price = $request->price;
        $product->type = $request->type;
        $product->status = 'available';
        $product->attributes = $request->attributes;
        if ($request->hasFile('mafile')) {
            $path = $request->file('mafile')->store('mafiles', 'private'); // ใช้ private disk เพื่อความปลอดภัย
            $product->mafile_path = $path;

            $mafileContent = file_get_contents($request->file('mafile')->path());
            $mafileData = json_decode($mafileContent, true);

            // ตรวจสอบว่าไฟล์มีรูปแบบถูกต้อง
            if (isset($mafileData['shared_secret']) && isset($mafileData['account_name'])) {
                // เก็บเฉพาะข้อมูลสำคัญที่จำเป็น
                $steamAuthData = [
                    'shared_secret' => $mafileData['shared_secret'],
                    'account_name' => $mafileData['account_name'],
                    'identity_secret' => $mafileData['identity_secret'] ?? null,
                ];

                // เข้ารหัสและบันทึก
                $product->steam_auth_data = encrypt(json_encode($steamAuthData));
            }
        }

        if ($request->filled('key_data')) {
            $product->key_data = Crypt::encryptString($request->key_data);
        }

        $product->save();

        return redirect()->route('seller.products.index')
            ->with('success', 'สร้างสินค้าใหม่เรียบร้อยแล้ว');
    }

    /**
     * แสดงฟอร์มแก้ไขสินค้า
     */
    public function edit(Product $product)
    {
        // ตรวจสอบว่าเป็นสินค้าของผู้ขายคนนี้หรือไม่
        if ($product->user_id !== auth()->id()) {
            return redirect()->route('seller.products.index')
                ->with('error', 'คุณไม่มีสิทธิ์แก้ไขสินค้านี้');
        }

        $categories = Category::all();

        return view('seller.products.edit', compact('product', 'categories'));
    }

    /**
     * อัพเดทข้อมูลสินค้า
     */
    public function update(Request $request, Product $product)
    {
        // ตรวจสอบว่าเป็นสินค้าของผู้ขายคนนี้หรือไม่
        if ($product->user_id !== auth()->id()) {
            return redirect()->route('seller.products.index')
                ->with('error', 'คุณไม่มีสิทธิ์แก้ไขสินค้านี้');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'type' => 'required|string|in:steam_key,origin_key,gog_key,uplay_key,battlenet_key,account',
            'status' => 'required|string|in:available,sold,pending',
            'attributes' => 'nullable|array',
            'key_data' => 'nullable|string',
        ]);

        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->type = $request->type;
        $product->status = $request->status;
        $product->attributes = $request->attributes;
        if ($request->filled('key_data')) {
            $product->key_data = Crypt::encryptString($request->key_data);
        }
        if ($request->hasFile('mafile')) {
            if ($product->mafile_path) {
                Storage::disk('private')->delete($product->mafile_path);
            }
            
            // เก็บไฟล์ใหม่
            $path = $request->file('mafile')->store('mafiles', 'private');
            $product->mafile_path = $path;
            
            $mafileContent = file_get_contents($request->file('mafile')->path());
            $mafileData = json_decode($mafileContent, true);

            // ตรวจสอบว่าไฟล์มีรูปแบบถูกต้อง
            if (isset($mafileData['shared_secret']) && isset($mafileData['account_name'])) {
                // เก็บเฉพาะข้อมูลสำคัญที่จำเป็น
                $steamAuthData = [
                    'shared_secret' => $mafileData['shared_secret'],
                    'account_name' => $mafileData['account_name'],
                    'identity_secret' => $mafileData['identity_secret'] ?? null,
                ];

                // เข้ารหัสและบันทึก
                $product->steam_auth_data = encrypt(json_encode($steamAuthData));
            }
        }
        $product->save();

        return redirect()->route('seller.products.index')
            ->with('success', 'อัพเดทสินค้าเรียบร้อยแล้ว');
    }

    /**
     * ลบสินค้า
     */
    public function destroy(Product $product)
    {
        // ตรวจสอบว่าเป็นสินค้าของผู้ขายคนนี้หรือไม่
        if ($product->user_id !== auth()->id()) {
            return redirect()->route('seller.products.index')
                ->with('error', 'คุณไม่มีสิทธิ์ลบสินค้านี้');
        }

        // ตรวจสอบว่าสินค้ายังไม่ถูกขายหรือถูกจองแล้ว
        if ($product->status !== 'available') {
            return redirect()->route('seller.products.index')
                ->with('error', 'ไม่สามารถลบสินค้าที่ถูกจองหรือขายแล้วได้');
        }

        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }
}
