<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('orders.index', compact('orders'));
    }

    /**
     * แสดงรายละเอียดของออเดอร์
     */
    public function show(Order $order)
    {
        // ตรวจสอบว่าเป็นออเดอร์ของผู้ใช้ปัจจุบันหรือไม่
        if ($order->user_id !== auth()->id()) {
            return redirect()->route('orders.index')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงออเดอร์นี้');
        }
        
        $orderItems = $order->orderItems()->with('product.user')->get();
        
        return view('orders.show', compact('order', 'orderItems'));
    }

    /**
     * ซื้อสินค้า
     */
    public function buy(Request $request, Product $product)
    {
        // ตรวจสอบว่าสินค้ายังพร้อมขายอยู่หรือไม่
        if ($product->status !== 'available') {
            return redirect()->route('products.show', $product)->with('error', 'สินค้านี้ไม่พร้อมขายในขณะนี้');
        }
        
        // สร้างออเดอร์ใหม่
        $order = new Order();
        $order->user_id = auth()->id();
        $order->order_number = 'ORD-' . Str::random(10);
        $order->total_amount = $product->price;
        $order->status = 'pending';
        $order->save();
        
        // สร้าง order item
        $orderItem = new OrderItem();
        $orderItem->order_id = $order->id;
        $orderItem->product_id = $product->id; // แก้ไขตรงนี้จาก $product->product_id เป็น $product->id
        $orderItem->price = $product->price;
        $orderItem->status = 'pending';
        $orderItem->save();
        
        // อัพเดทสถานะสินค้า
        $product->status = 'pending';
        $product->save();
        
        // ไปที่หน้าชำระเงิน
        return redirect()->route('checkout', $order);
    }
}
