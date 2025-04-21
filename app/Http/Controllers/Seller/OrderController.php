<?php

namespace App\Http\Controllers\Seller;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderItems = OrderItem::whereHas('product', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['order', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('seller.orders.index', compact('orderItems'));
    }

    /**
     * แสดงรายละเอียดออเดอร์
     */
    public function show(Order $order)
    {
        // ตรวจสอบว่ามีสินค้าของผู้ขายในออเดอร์นี้หรือไม่
        $hasSellerItems = $order->orderItems()->whereHas('product', function ($query) {
            $query->where('user_id', auth()->id());
        })->exists();
        
        if (!$hasSellerItems) {
            return redirect()->route('seller.orders.index')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงออเดอร์นี้');
        }
        
        $orderItems = $order->orderItems()->whereHas('product', function ($query) {
            $query->where('user_id', auth()->id());
        })->with('product')->get();
        
        $buyer = $order->user;
        
        return view('seller.orders.show', compact('order', 'orderItems', 'buyer'));
    }

    /**
     * ส่งมอบรหัสเกมให้ลูกค้า
     */
    public function deliverKey(Request $request, OrderItem $orderItem)
    {
        // ตรวจสอบว่าเป็นสินค้าของผู้ขายคนนี้หรือไม่
        if ($orderItem->product->user_id !== auth()->id()) {
            return redirect()->route('seller.orders.index')
                ->with('error', 'คุณไม่มีสิทธิ์จัดการออเดอร์นี้');
        }
        
        // ตรวจสอบว่าออเดอร์อยู่ในสถานะที่พร้อมส่งมอบหรือไม่
        if ($orderItem->order->status !== 'processing' || $orderItem->status !== 'pending') {
            return redirect()->route('seller.orders.show', $orderItem->order)
                ->with('error', 'ไม่สามารถส่งมอบรหัสในขณะนี้ได้');
        }
        
        $request->validate([
            'key_data' => 'required|string',
        ]);
        
        // เข้ารหัสข้อมูลรหัสเกม
        $encryptedKey = Crypt::encryptString($request->key_data);
        
        // อัพเดทข้อมูล
        $orderItem->key_data = $encryptedKey;
        $orderItem->status = 'delivered';
        $orderItem->delivered_at = now();
        $orderItem->save();
        
        // อัพเดทสถานะสินค้า
        $orderItem->product->status = 'sold';
        $orderItem->product->save();
        
        // ตรวจสอบว่าทุกรายการในออเดอร์ถูกส่งมอบแล้วหรือยัง
        $pendingItems = $orderItem->order->orderItems()->where('status', 'pending')->count();
        if ($pendingItems === 0) {
            $orderItem->order->status = 'completed';
            $orderItem->order->save();
        }
        
        return redirect()->route('seller.orders.show', $orderItem->order)
            ->with('success', 'ส่งมอบรหัสเกมให้ลูกค้าเรียบร้อยแล้ว');
    }
}
