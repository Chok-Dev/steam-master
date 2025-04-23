<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function destroy(Order $order)
    {
        // ตรวจสอบว่าเป็นออเดอร์ของผู้ใช้ปัจจุบันหรือไม่
        if ($order->user_id !== auth()->id()) {
            return redirect()->route('orders.index')->with('error', 'คุณไม่มีสิทธิ์ลบออเดอร์นี้');
        }

        // ตรวจสอบว่าออเดอร์อยู่ในสถานะที่สามารถลบได้หรือไม่ (เช่น 'pending' และยังไม่ชำระเงิน)
        if ($order->status !== 'pending' || $order->isPaid()) {
            return redirect()->route('orders.show', $order)->with('error', 'ไม่สามารถลบออเดอร์ที่ชำระเงินแล้วหรืออยู่ในระหว่างดำเนินการได้');
        }

        // ใช้ database transaction เพื่อให้แน่ใจว่าการลบข้อมูลทั้งหมดสำเร็จหรือล้มเหลวพร้อมกัน
        DB::beginTransaction();

        try {
            // 1. อัพเดทสถานะสินค้าให้กลับเป็น available
            foreach ($order->orderItems as $item) {
                $item->product->update(['status' => 'available']);
            }

            // 2. ลบรายการสินค้าในออเดอร์ก่อน (ลบ child records)
            $order->orderItems()->delete();

            // 3. จากนั้นลบออเดอร์ (ลบ parent record)
            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')->with('success', 'ลบออเดอร์เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            // หากเกิดข้อผิดพลาด ยกเลิกการทำรายการทั้งหมด
            DB::rollBack();

            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการลบออเดอร์: ' . $e->getMessage());
        }
    }
}
